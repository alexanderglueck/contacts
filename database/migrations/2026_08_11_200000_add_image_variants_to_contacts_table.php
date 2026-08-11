<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Larger renditions of a contact's photo, so viewers can show real detail and
 * offer a download instead of upscaling the 400x400 avatar.
 *
 * `image` keeps its exact current meaning — the square 400x400 avatar — so
 * nothing that already reads it has to change. The two new columns are
 * uncropped, aspect-preserving renditions and are nullable because they can
 * only ever exist for images uploaded after this ships: the pipeline discarded
 * the source after cropping, so nothing can be backfilled.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('image_medium')->nullable()->after('image');
            $table->string('image_full')->nullable()->after('image_medium');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['image_medium', 'image_full']);
        });
    }
};
