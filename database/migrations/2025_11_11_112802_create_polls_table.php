<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PollStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title' , 500);
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedTinyInteger('status')->default(PollStatus::Draft->value);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
