<?php

namespace App\Services;

use App\Mail\ServiceContractSignatureMail;
use App\Models\Customer;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ServiceContractService
{
    public function createAndSend(Customer $customer, User $actor, array $options): Document
    {
        $customer->loadMissing('enterprise', 'user');

        if (blank($customer->email)) {
            throw new RuntimeException('O cliente precisa ter e-mail para receber o contrato.');
        }

        $template = $this->resolveTemplate();
        $signer = $this->resolveSigner($customer, $actor, $options);
        $city = trim((string) ($options['city'] ?? $customer->city ?? ''));
        $subject = trim((string) ($options['subject'] ?? 'prestacao de servicos advocaticios'));
        $generatedAt = now();

        $html = $this->renderHtml($customer, $signer, [
            'subject' => $subject,
            'city' => $city !== '' ? $city : '________________',
            'date' => $generatedAt->format('d/m/Y'),
        ]);

        $directory = 'documents/service-contracts';
        Storage::disk('public')->makeDirectory($directory);

        $filename = 'contrato-prestacao-servicos-' . $customer->id . '-' . $generatedAt->format('YmdHis') . '.pdf';
        $relativePath = $directory . '/' . $filename;
        $absolutePath = Storage::disk('public')->path($relativePath);
        $absoluteDirectory = dirname($absolutePath);

        if (! is_dir($absoluteDirectory)) {
            mkdir($absoluteDirectory, 0777, true);
        }

        Pdf::loadHTML($html)->save($absolutePath);

        $document = Document::create([
            'enterprise_id' => $customer->enterprise_id ?? $actor->enterprise_id,
            'title' => 'Contrato de prestação de serviços - ' . $customer->name,
            'type' => 'contract',
            'document_link' => Storage::disk('public')->url($relativePath),
            'document_template_id' => $template->id,
            'customer_id' => $customer->id,
        ]);

        $cc = collect([$signer['email'] ?? null, $actor->email ?? null])
            ->filter()
            ->map(fn ($email) => mb_strtolower(trim((string) $email)))
            ->reject(fn ($email) => $email === mb_strtolower(trim((string) $customer->email)))
            ->unique()
            ->values()
            ->all();

        Mail::to($customer->email)
            ->cc($cc)
            ->send(new ServiceContractSignatureMail(
                customer: $customer,
                document: $document,
                signer: $signer,
                attachmentPath: $absolutePath
            ));

        return $document;
    }

    private function resolveTemplate(): DocumentTemplate
    {
        return DocumentTemplate::query()->firstOrCreate(
            [
                'title' => 'Contrato de prestação de serviços advocatícios (automático)',
                'type' => 'contract',
            ],
            [
                'date' => now()->toDateString(),
                'content' => $this->defaultTemplateContent(),
            ]
        );
    }

    private function resolveSigner(Customer $customer, User $actor, array $options): array
    {
        $signerType = $options['signer_type'] ?? 'enterprise';
        $enterprise = $customer->enterprise ?? $actor->enterprise;

        if ($signerType === 'lawyer') {
            /** @var User $signerUser */
            $signerUser = $options['signer_user'];
            $oab = $this->formatOab($signerUser);
            $qualification = $oab !== null
                ? 'advogado(a) inscrito(a) na OAB/' . $signerUser->oab_state . ' sob o nº ' . $signerUser->oab_number
                : 'advogado(a)';

            return [
                'type' => 'lawyer',
                'name' => $signerUser->name,
                'email' => $signerUser->email,
                'qualification' => $qualification,
                'document' => $oab ?? 'OAB não informada',
                'address' => $enterprise?->address ?: 'endereço profissional não informado',
                'signature_label' => $signerUser->name,
                'counterparty_label' => 'Advogado(a)',
            ];
        }

        $enterpriseDocument = $enterprise?->cnp
            ? 'CNPJ ' . $this->formatCpfOrCnpj($enterprise->cnp)
            : 'CNPJ não informado';
        $representative = $actor->name ? 'neste ato representado por ' . $actor->name : null;

        return [
            'type' => 'enterprise',
            'name' => $enterprise?->name ?: 'Escritório não informado',
            'email' => $enterprise?->email ?: $actor->email,
            'qualification' => collect(['sociedade de advocacia', $representative])->filter()->implode(', '),
            'document' => $enterpriseDocument,
            'address' => $enterprise?->address ?: 'endereço do escritório não informado',
            'signature_label' => $enterprise?->name ?: 'Escritório',
            'counterparty_label' => 'Escritório',
        ];
    }

    private function renderHtml(Customer $customer, array $signer, array $data): string
    {
        $customerDocument = $customer->cnp
            ? $this->formatCpfOrCnpj($customer->cnp)
            : 'não informado';
        $customerAddress = $this->formatCustomerAddress($customer);

        $body = '
            <h1 style="text-align:center;font-size:18px;margin-bottom:24px;">CONTRATO DE PRESTAÇÃO DE SERVIÇOS ADVOCATÍCIOS</h1>
            <p><strong>CONTRATANTE:</strong> ' . e($customer->name) . ', inscrito(a) no CPF/CNPJ sob o nº ' . e($customerDocument) . ', residente e domiciliado(a) em ' . e($customerAddress) . '.</p>
            <p><strong>CONTRATADO(A):</strong> ' . e($signer['name']) . ', ' . e($signer['qualification']) . ', ' . e($signer['document']) . ', com endereço em ' . e($signer['address']) . '.</p>
            <p><strong>CLÁUSULA 1 - OBJETO.</strong> O presente contrato tem por objeto a prestação de serviços advocatícios relacionados a ' . e($data['subject']) . '.</p>
            <p><strong>CLÁUSULA 2 - HONORÁRIOS.</strong> Os honorários e a forma de pagamento serão observados conforme proposta comercial, termo complementar ou ajuste firmado entre as partes.</p>
            <p><strong>CLÁUSULA 3 - OBRIGAÇÕES.</strong> O(a) CONTRATADO(A) compromete-se a executar os serviços com zelo técnico e profissional, e o(a) CONTRATANTE compromete-se a fornecer documentos, informações e autorizações necessárias ao regular andamento da demanda.</p>
            <p><strong>CLÁUSULA 4 - COMUNICAÇÕES.</strong> As partes reconhecem o e-mail como meio válido para envio deste instrumento e demais comunicações relacionadas à contratação.</p>
            <p><strong>CLÁUSULA 5 - ACEITE.</strong> O recebimento deste contrato por e-mail tem como finalidade viabilizar a assinatura e formalização da contratação entre cliente e ' . e(mb_strtolower($signer['counterparty_label'])) . '.</p>
            <p style="margin-top:24px;">' . e($data['city']) . ', ' . e($data['date']) . '.</p>
            <table style="width:100%;margin-top:48px;border-collapse:collapse;">
                <tr>
                    <td style="width:50%;padding-right:16px;vertical-align:top;">
                        <div style="border-top:1px solid #000;padding-top:8px;text-align:center;">' . e($customer->name) . '<br><span style="font-size:11px;">CONTRATANTE</span></div>
                    </td>
                    <td style="width:50%;padding-left:16px;vertical-align:top;">
                        <div style="border-top:1px solid #000;padding-top:8px;text-align:center;">' . e($signer['signature_label']) . '<br><span style="font-size:11px;">CONTRATADO(A)</span></div>
                    </td>
                </tr>
            </table>
        ';

        return '<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:12pt;line-height:1.55;margin:2cm;color:#111;} p{margin:0 0 14px 0;} strong{font-weight:700;}</style></head><body>' . $body . '</body></html>';
    }

    private function defaultTemplateContent(): string
    {
        return "CONTRATO DE PRESTAÇÃO DE SERVIÇOS ADVOCATÍCIOS\n\nCONTRATANTE: {{nome_cliente}}\nCONTRATADO(A): {{nome_contratado}}\nOBJETO: {{objeto_contrato}}\nCIDADE: {{cidade}}\nDATA: {{data}}";
    }

    private function formatCustomerAddress(Customer $customer): string
    {
        $parts = array_filter([
            $customer->street,
            $customer->number,
            $customer->neighborhood,
            $customer->city ? ($customer->city . ($customer->state ? '/' . $customer->state : '')) : null,
            $customer->zip_code,
        ]);

        return $parts !== [] ? implode(', ', $parts) : 'endereço não informado';
    }

    private function formatCpfOrCnpj(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $digits) ?: $digits;
        }

        if (strlen($digits) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits) ?: $digits;
        }

        return $value;
    }

    private function formatOab(User $user): ?string
    {
        if (blank($user->oab_state) || blank($user->oab_number)) {
            return null;
        }

        return 'OAB/' . strtoupper((string) $user->oab_state) . ' ' . $user->oab_number;
    }
}
