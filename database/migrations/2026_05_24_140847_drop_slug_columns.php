<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Slug columns are no longer routing keys (replaced by ulid) and the
     * Sluggable trait has been retired from every model that had it. Drop
     * the columns and their unique indexes.
     */
    private array $tables = [
        'contacts',
        'users',
        'roles',
        'announcements',
        'news',
        'contact_groups',
        'contact_addresses',
        'contact_numbers',
        'contact_emails',
        'contact_urls',
        'contact_notes',
        'contact_dates',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if ( ! Schema::hasColumn($table, 'slug')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('slug');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'slug')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->string('slug')->nullable();
            });
        }
    }
};
