<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('activated', true)
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_api_token_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['activated', 'google2fa_secret', 'api_token']);
        });

        Schema::dropIfExists('tfa_backup_codes');
        Schema::dropIfExists('confirmation_tokens');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('activated')->default(false);
            $table->string('google2fa_secret', 64)->nullable();
            $table->string('api_token', 60)->unique()->nullable();
        });

        DB::table('users')
            ->whereNotNull('email_verified_at')
            ->update(['activated' => true]);

        Schema::create('tfa_backup_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->unique(['user_id', 'value']);
            $table->timestamps();
        });

        Schema::create('confirmation_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token');
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }
};
