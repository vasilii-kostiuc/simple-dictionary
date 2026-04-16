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
        Schema::create('match_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->uuid('guest_id')->nullable();
            $table->string('participant_name');
            $table->string('participant_avatar')->nullable();
            $table->integer('score')->default(0);
            $table->integer('answered_count')->default(0);
            $table->integer('correct_answers_count')->default(0);
            $table->integer('place')->nullable();
            $table->boolean('is_winner')->default(false);
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(['match_id', 'user_id']);
            $table->unique(['match_id', 'guest_id']);
            $table->index(['guest_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_users');
    }
};
