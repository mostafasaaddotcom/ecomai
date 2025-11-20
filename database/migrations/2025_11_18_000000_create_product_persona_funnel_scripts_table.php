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
        Schema::create('product_persona_funnel_scripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_persona_id')->constrained()->onDelete('cascade');
            $table->enum('stage', ['unaware', 'problem_aware', 'solution_aware', 'product_aware', 'most_aware']);
            $table->string('angle')->nullable();
            $table->string('formula')->nullable();
            $table->string('language')->nullable();
            $table->string('tone')->nullable();
            $table->text('content')->nullable();
            $table->string('voice_link_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_persona_funnel_scripts');
    }
};
