<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Firebase Messaging 25.1.0 deprecated registration tokens on the client in
 * favour of Firebase Installation IDs, so a device is now identified by its
 * FID rather than by a token that rotates.
 *
 * The column is nullable and sits alongside device_token rather than replacing
 * it: the Android app sends both during the transition, so existing rows adopt
 * a FID as their device checks in, and the token stays available both as a
 * fallback for older app builds and as a rollback value.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('fid')->nullable()->after('device_token');

            // Registration looks a device up by fid before falling back to token.
            $table->index('fid');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex(['fid']);
            $table->dropColumn('fid');
        });
    }
};
