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
        Schema::create('product_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('angle')->nullable();
            $table->enum('type', ['ugc', 'expert', 'background_voice']);
            $table->string('formula')->nullable();
            $table->string('language')->nullable();
            $table->string('tone')->nullable();
            $table->text('content')->nullable();
            $table->string('voice_url_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_copies');
    }
};
