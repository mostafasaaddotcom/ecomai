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
        Schema::table('ad_creatives', function (Blueprint $table) {
            $table->text('original_video_url')->nullable()->after('type');
            $table->text('processed_video_url')->nullable()->after('original_video_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ad_creatives', function (Blueprint $table) {
            $table->dropColumn(['original_video_url', 'processed_video_url']);
        });
    }
};
