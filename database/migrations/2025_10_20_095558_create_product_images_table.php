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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['product_only', 'lifestyle', 'ugc_scene', 'expert', 'other']);
            $table->text('prompt')->nullable();
            $table->string('image_url')->nullable();
            $table->string('aspect_ratio')->nullable();
            $table->boolean('is_ai_generated')->default(true);
            $table->string('reference_id')->nullable();
            $table->enum('status', ['prompt_generated', 'image_generating', 'completed', 'failed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
