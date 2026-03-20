<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_from_id')->constrained('languages');
            $table->foreignId('language_to_id')->constrained('languages');
            $table->foreignId('dictionary_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('match_type', ['time', 'steps']);
            $table->json('match_type_params');
            $table->enum('status', ['new', 'in_progress', 'completed', 'cancelled'])->default('new');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('completion_reason')->nullable();
            $table->json('completion_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
