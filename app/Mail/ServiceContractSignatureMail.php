<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceContractSignatureMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public Document $document,
        public array $signer,
        public string $attachmentPath,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Assinatura pendente: contrato de prestacao de servicos',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-contract-signature',
        );
    }

    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromPath($this->attachmentPath)
                ->as('contrato-prestacao-servicos.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
