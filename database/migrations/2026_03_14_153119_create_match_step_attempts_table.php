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
        Schema::create('match_step_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_step_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number');
            $table->integer('sub_index');
            $table->json('attempt_data');
            $table->boolean('is_correct');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_step_attempts');
    }
};
