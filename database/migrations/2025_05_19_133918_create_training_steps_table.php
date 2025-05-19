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
        Schema::create('training_steps', function (Blueprint $table) {
            $table->id();
            $table->integer('step_number');
            $table->foreignIdFor(App\Models\Training\Training::class);
            $table->integer('step_type_id');
            $table->json('step_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_steps');
    }
};
