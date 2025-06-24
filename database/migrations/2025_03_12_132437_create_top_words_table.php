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
        Schema::create('top_words', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Domain\Language\Models\Language::class, 'language_from_id');
            $table->foreignIdFor(\App\Domain\Language\Models\Language::class, 'language_to_id');
            $table->string('word');
            $table->string('translation');
            $table->timestamps();

            $table->unique(['language_from_id', 'language_to_id', 'word']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_words');
    }
};
