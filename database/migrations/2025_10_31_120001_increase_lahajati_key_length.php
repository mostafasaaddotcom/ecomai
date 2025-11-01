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
        Schema::table('api_service_keys', function (Blueprint $table) {
            $table->text('lahajati_key')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_service_keys', function (Blueprint $table) {
            $table->string('lahajati_key')->nullable()->change();
        });
    }
};
