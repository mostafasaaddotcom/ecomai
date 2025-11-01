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
        Schema::create('api_service_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('openrouter_key')->nullable();
            $table->string('kie_key')->nullable();
            $table->string('lahajati_key')->nullable();
            $table->string('supabase_project_url')->nullable();
            $table->string('supabase_service_role_key')->nullable();
            $table->timestamps();

            // Ensure one record per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_service_keys');
    }
};
