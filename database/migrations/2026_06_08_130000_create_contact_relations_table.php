<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_relations', function (Blueprint $table) {
            $table->id();
            $table->char('ulid', 26)->unique();
            // Pair always stored with contact_a_id < contact_b_id (labels
            // swapped accordingly on write) so a single unique key catches
            // the pair regardless of which contact the link was created from.
            $table->foreignId('contact_a_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('contact_b_id')->constrained('contacts')->cascadeOnDelete();
            // Direction labels are stored, not derived: a_to_b_label is what
            // B is to A (shown on A's page); b_to_a_label is what A is to B
            // (shown on B's page). Free text — no gender/type lookup.
            $table->string('a_to_b_label');
            $table->string('b_to_a_label');
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'contact_a_id', 'contact_b_id'], 'contact_relations_pair_unique');
            $table->index(['team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_relations');
    }
};
