<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->string('completion_type')->default('manual')->after('training_type_id');
            $table->json('completion_type_params')->nullable()->after('completion_type');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('completion_type_params');
            $table->dropColumn('completion_type');
            $table->dropColumn('started_at');
            $table->dropColumn('completed_at');
        });
    }
};
