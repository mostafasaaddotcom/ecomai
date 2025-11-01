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
        Schema::create('user_lahajati_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lahajati_performance_id')->constrained()->onDelete('cascade');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'lahajati_performance_id'], 'user_performance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_lahajati_performances');
    }
};
