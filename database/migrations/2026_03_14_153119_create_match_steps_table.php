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
        Schema::create('match_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->uuid('guest_id')->nullable();
            $table->integer('step_type_id');
            $table->integer('step_number');
            $table->json('step_data');
            $table->integer('required_answers_count')->default(1);
            $table->boolean('skipped')->default(false);
            $table->timestamp('skipped_at')->nullable();
            $table->timestamps();

            $table->index(['match_id', 'user_id', 'step_number']);
            $table->index(['match_id', 'guest_id', 'step_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_steps');
    }
};
