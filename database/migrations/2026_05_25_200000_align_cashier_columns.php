<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Brings older Cashier-12-era schemas into line with what Cashier 15/16 expects.
// Each step is guarded so it's a no-op on a fresh database that was already
// created with the current `create_subscriptions_table` migration.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                // Cashier 15: `name` → `type`.
                if (Schema::hasColumn('subscriptions', 'name') && ! Schema::hasColumn('subscriptions', 'type')) {
                    $table->renameColumn('name', 'type');
                }

                // Cashier 13: `stripe_plan` → `stripe_price`.
                if (Schema::hasColumn('subscriptions', 'stripe_plan') && ! Schema::hasColumn('subscriptions', 'stripe_price')) {
                    $table->renameColumn('stripe_plan', 'stripe_price');
                }
            });
        }

        if (Schema::hasTable('subscription_items')) {
            Schema::table('subscription_items', function (Blueprint $table) {
                // Cashier 13: `stripe_plan` → `stripe_price`.
                if (Schema::hasColumn('subscription_items', 'stripe_plan') && ! Schema::hasColumn('subscription_items', 'stripe_price')) {
                    $table->renameColumn('stripe_plan', 'stripe_price');
                }
            });

            // Cashier 13 added stripe_product. Existing rows have no value for it;
            // Cashier repopulates it on the next webhook / subscription sync, and
            // only subscribedToProduct() reads it — so nullable is fine.
            if (! Schema::hasColumn('subscription_items', 'stripe_product')) {
                Schema::table('subscription_items', function (Blueprint $table) {
                    $table->string('stripe_product')->nullable()->after('stripe_id');
                });
            }
        }
    }

    public function down(): void
    {
        // Intentionally not reversed: this migration only flips columns that
        // were already in their post-Cashier-15 shape on fresh installs.
    }
};
