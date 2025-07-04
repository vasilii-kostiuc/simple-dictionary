<?php

use App\Domain\Training\Models\TrainingStep;
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
        Schema::create('training_step_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TrainingStep::class);
            $table->json('attempt_data')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('attempt_number')->default(1);
            $table->integer('sub_index')->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_step_attempts');
    }
};
