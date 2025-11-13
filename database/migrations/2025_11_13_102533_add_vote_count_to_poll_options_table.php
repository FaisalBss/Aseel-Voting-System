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
        Schema::table('poll_options', function (Blueprint $table) {
            $table->unsignedInteger('vote_count')->default(0)->after('option_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poll_options', function (Blueprint $table) {
            $table->dropColumn('vote_count');
        });
    }
};
