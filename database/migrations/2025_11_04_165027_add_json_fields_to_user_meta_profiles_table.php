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
        Schema::table('user_meta_profiles', function (Blueprint $table) {
            $table->json('campaigns_available_for_duplicate')->nullable()->after('is_default');
            $table->json('ad_sets_available_for_duplicate')->nullable()->after('campaigns_available_for_duplicate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_meta_profiles', function (Blueprint $table) {
            $table->dropColumn(['campaigns_available_for_duplicate', 'ad_sets_available_for_duplicate']);
        });
    }
};
