<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('match_invites', 'match_links');
    }

    public function down(): void
    {
        Schema::rename('match_links', 'match_invites');
    }
};
