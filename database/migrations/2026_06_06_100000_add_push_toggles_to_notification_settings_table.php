<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->boolean('send_daily_push')->default(false)->after('send_daily');
            $table->boolean('send_weekly_push')->default(false)->after('send_weekly');
        });
    }

    public function down(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropColumn(['send_daily_push', 'send_weekly_push']);
        });
    }
};
