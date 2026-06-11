<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('enterprises')) {
            Schema::create('enterprises', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('cnp')->nullable()->unique();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('users', 'enterprise_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('enterprise_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('enterprises')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 32)
                    ->default('admin')
                    ->after('password');
            });
        }

        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('role');
            });
        }

        if (! Schema::hasColumn('customers', 'user_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('customers', 'enterprise_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreignId('enterprise_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('enterprises')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('documents', 'enterprise_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->foreignId('enterprise_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('enterprises')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('tasks', 'enterprise_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('enterprise_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('enterprises')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('datajud_processos', 'enterprise_id')) {
            Schema::table('datajud_processos', function (Blueprint $table) {
                $table->foreignId('enterprise_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('enterprises')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasColumn('customers', 'password') && Schema::hasColumn('customers', 'user_id')) {
            $customersWithAccess = DB::table('customers')
                ->whereNotNull('email')
                ->whereNotNull('password')
                ->select(['id', 'name', 'email', 'password', 'remember_token', 'created_at', 'updated_at'])
                ->get();

            foreach ($customersWithAccess as $customer) {
                $user = DB::table('users')->where('email', $customer->email)->first();

                if (! $user) {
                    $userId = DB::table('users')->insertGetId([
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'password' => $customer->password,
                        'remember_token' => $customer->remember_token,
                        'role' => 'client',
                        'is_active' => true,
                        'created_at' => $customer->created_at ?? now(),
                        'updated_at' => $customer->updated_at ?? now(),
                    ]);
                } else {
                    $userId = $user->id;
                }

                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update([
                        'user_id' => $userId,
                        'updated_at' => now(),
                    ]);
            }
        }

        if (Schema::hasColumn('customers', 'user_id') && Schema::hasColumn('customers', 'enterprise_id')) {
            DB::table('customers')
                ->whereNotNull('user_id')
                ->orderBy('id')
                ->chunkById(100, function ($customers): void {
                    foreach ($customers as $customer) {
                        $enterpriseId = DB::table('users')
                            ->where('id', $customer->user_id)
                            ->value('enterprise_id');

                        DB::table('customers')
                            ->where('id', $customer->id)
                            ->update(['enterprise_id' => $enterpriseId]);
                    }
                });
        }

        if (Schema::hasColumn('documents', 'customer_id') && Schema::hasColumn('documents', 'enterprise_id')) {
            DB::table('documents')
                ->whereNotNull('customer_id')
                ->orderBy('id')
                ->chunkById(100, function ($documents): void {
                    foreach ($documents as $document) {
                        $enterpriseId = DB::table('customers')
                            ->where('id', $document->customer_id)
                            ->value('enterprise_id');

                        DB::table('documents')
                            ->where('id', $document->id)
                            ->update(['enterprise_id' => $enterpriseId]);
                    }
                });
        }

        if (Schema::hasColumn('datajud_processos', 'user_id') && Schema::hasColumn('datajud_processos', 'enterprise_id')) {
            DB::table('datajud_processos')
                ->whereNotNull('user_id')
                ->orderBy('id')
                ->chunkById(100, function ($processos): void {
                    foreach ($processos as $processo) {
                        $enterpriseId = DB::table('users')
                            ->where('id', $processo->user_id)
                            ->value('enterprise_id');

                        DB::table('datajud_processos')
                            ->where('id', $processo->id)
                            ->update(['enterprise_id' => $enterpriseId]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('datajud_processos', 'enterprise_id')) {
            Schema::table('datajud_processos', function (Blueprint $table) {
                $table->dropConstrainedForeignId('enterprise_id');
            });
        }

        if (Schema::hasColumn('tasks', 'enterprise_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropConstrainedForeignId('enterprise_id');
            });
        }

        if (Schema::hasColumn('documents', 'enterprise_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropConstrainedForeignId('enterprise_id');
            });
        }

        if (Schema::hasColumn('customers', 'enterprise_id') || Schema::hasColumn('customers', 'user_id')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'enterprise_id')) {
                    $table->dropConstrainedForeignId('enterprise_id');
                }
                if (Schema::hasColumn('customers', 'user_id')) {
                    $table->dropConstrainedForeignId('user_id');
                }
            });
        }

        if (Schema::hasColumn('users', 'enterprise_id') || Schema::hasColumn('users', 'role') || Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'enterprise_id')) {
                    $table->dropConstrainedForeignId('enterprise_id');
                }
                $dropColumns = [];
                if (Schema::hasColumn('users', 'role')) {
                    $dropColumns[] = 'role';
                }
                if (Schema::hasColumn('users', 'is_active')) {
                    $dropColumns[] = 'is_active';
                }
                if ($dropColumns !== []) {
                    $table->dropColumn($dropColumns);
                }
            });
        }

        if (Schema::hasTable('enterprises')) {
            Schema::dropIfExists('enterprises');
        }
    }
};
