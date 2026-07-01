<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('billing_settings')) {
            Schema::create('billing_settings', function (Blueprint $table) {
                $table->id();
                $table->string('stripe_publishable_key')->nullable();
                $table->text('stripe_secret_key')->nullable();
                $table->text('stripe_webhook_secret')->nullable();
                $table->string('default_currency', 3)->default('brl');
                $table->boolean('is_stripe_enabled')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('saas_plans')) {
            Schema::create('saas_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('price_cents')->nullable();
                $table->string('currency', 3)->default('brl');
                $table->string('billing_interval', 10)->default('month');
                $table->unsignedSmallInteger('interval_count')->default(1);
                $table->unsignedSmallInteger('trial_days')->nullable();
                $table->string('button_label')->nullable();
                $table->json('features')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_public')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->boolean('contact_only')->default(false);
                $table->integer('sort_order')->default(0);
                $table->string('stripe_product_id')->nullable();
                $table->string('stripe_price_id')->nullable();
                $table->string('stripe_price_signature')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('enterprises')) {
            Schema::table('enterprises', function (Blueprint $table) {
                if (! Schema::hasColumn('enterprises', 'subscription_plan_id')) {
                    $table->foreignId('subscription_plan_id')
                        ->nullable()
                        ->constrained('saas_plans')
                        ->nullOnDelete();
                }

                if (! Schema::hasColumn('enterprises', 'stripe_customer_id')) {
                    $table->string('stripe_customer_id')->nullable();
                }

                if (! Schema::hasColumn('enterprises', 'stripe_subscription_id')) {
                    $table->string('stripe_subscription_id')->nullable();
                }

                if (! Schema::hasColumn('enterprises', 'stripe_price_id')) {
                    $table->string('stripe_price_id')->nullable();
                }

                if (! Schema::hasColumn('enterprises', 'subscription_status')) {
                    $table->string('subscription_status')->nullable();
                }

                if (! Schema::hasColumn('enterprises', 'subscription_started_at')) {
                    $table->timestamp('subscription_started_at')->nullable();
                }

                if (! Schema::hasColumn('enterprises', 'subscription_ends_at')) {
                    $table->timestamp('subscription_ends_at')->nullable();
                }

                if (! Schema::hasColumn('enterprises', 'trial_ends_at')) {
                    $table->timestamp('trial_ends_at')->nullable();
                }

                if (! Schema::hasColumn('enterprises', 'subscription_canceled_at')) {
                    $table->timestamp('subscription_canceled_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('enterprises')) {
            Schema::table('enterprises', function (Blueprint $table) {
                if (Schema::hasColumn('enterprises', 'subscription_plan_id')) {
                    $table->dropConstrainedForeignId('subscription_plan_id');
                }

                $columns = [];

                foreach ([
                    'stripe_customer_id',
                    'stripe_subscription_id',
                    'stripe_price_id',
                    'subscription_status',
                    'subscription_started_at',
                    'subscription_ends_at',
                    'trial_ends_at',
                    'subscription_canceled_at',
                ] as $column) {
                    if (Schema::hasColumn('enterprises', $column)) {
                        $columns[] = $column;
                    }
                }

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        Schema::dropIfExists('saas_plans');
        Schema::dropIfExists('billing_settings');
    }
};
