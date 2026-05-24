<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        Role::withoutEvents(function () {
            Role::withoutGlobalScopes()->whereNull('ulid')->cursor()->each(function (Role $role) {
                $role->ulid = (string) Str::ulid();
                $role->saveQuietly();
            });
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};
