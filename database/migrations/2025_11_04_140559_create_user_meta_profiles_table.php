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
        Schema::create('user_meta_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('ad_account_id');
            $table->string('facebook_page_id');
            $table->string('instagram_profile_id')->nullable();
            $table->text('access_token');
            $table->string('facebook_pixel');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'ad_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_meta_profiles');
    }
};
