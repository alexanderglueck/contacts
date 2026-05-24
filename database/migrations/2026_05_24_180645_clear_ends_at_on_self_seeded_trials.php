<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Older CreateNewUser revisions wrote `ends_at = trial_ends_at` on
        // seeded trial subscriptions. Cashier treats any non-null ends_at as
        // a cancellation (with grace period), which makes every "is canceled"
        // check return true for active trial users.
        //
        // Heuristic: stripe_id starts with our 'trial_' marker, status is
        // active, and ends_at matches trial_ends_at (i.e. it wasn't a real
        // cancellation that happened to coincide with trial end).
        DB::table('subscriptions')
            ->where('stripe_id', 'like', 'trial_%')
            ->where('stripe_status', 'active')
            ->whereColumn('ends_at', 'trial_ends_at')
            ->update(['ends_at' => null]);
    }

    public function down(): void
    {
        // Restore the (broken) original shape so down() composes cleanly with
        // the CreateNewUser change. Only touches the same rows.
        DB::table('subscriptions')
            ->where('stripe_id', 'like', 'trial_%')
            ->where('stripe_status', 'active')
            ->whereNull('ends_at')
            ->whereNotNull('trial_ends_at')
            ->update(['ends_at' => DB::raw('trial_ends_at')]);
    }
};
