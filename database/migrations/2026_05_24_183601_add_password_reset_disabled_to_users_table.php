<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Per-user opt-out for the password-reset flow. When set, the user
     * model short-circuits sendPasswordResetNotification, so even if
     * someone hits POST /forgot-password with the user's email, no
     * email is sent. The password broker still returns its usual OK
     * response, so the public endpoint reveals nothing.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('password_reset_disabled')->default(false)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_reset_disabled');
        });
    }
};
