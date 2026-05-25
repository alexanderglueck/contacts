<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_non_duplicates', function (Blueprint $table) {
            $table->id();
            // Pair always stored with contact_a_id < contact_b_id so a unique
            // key on (team_id, a, b) catches the pair regardless of click order.
            $table->foreignId('contact_a_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('contact_b_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'contact_a_id', 'contact_b_id'], 'cnd_pair_unique');
            $table->index(['team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_non_duplicates');
    }
};
