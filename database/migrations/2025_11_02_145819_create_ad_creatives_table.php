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
        Schema::create('ad_creatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['image', 'video']);
            $table->string('page_id')->nullable();
            $table->string('instagram_user_id')->nullable();
            $table->string('video_id')->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->enum('call_to_action_type', [
                'SHOP_NOW',
                'LEARN_MORE',
                'SIGN_UP',
                'BUY_NOW',
                'CONTACT_US',
                'DOWNLOAD',
                'BOOK_TRAVEL',
                'APPLY_NOW',
                'SUBSCRIBE',
                'GET_QUOTE',
                'WATCH_MORE',
                'SEE_MENU',
                'CALL_NOW',
                'MESSAGE_PAGE',
                'SEND_MESSAGE',
                'WHATSAPP_MESSAGE',
                'GET_OFFER',
                'GET_SHOWTIMES',
            ])->nullable();
            $table->text('call_to_action_link')->nullable();
            $table->string('creative_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_creatives');
    }
};
