<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('user_id');
            $table->string('business_name')->length(50)->nullable();
            $table->string('telephone')->length(50)->nullable();

            $table->string('email')->length(50)->nullable();
            $table->string('email_host')->length(50)->nullable();
            $table->string('website_url')->length(100)->nullable();
            $table->string('linkedin')->length(255)->nullable();
            $table->string('facebook_profile')->length(255)->nullable();
            $table->string('facebook_messenger')->length(255)->nullable();
            $table->string('instagram')->length(255)->nullable();
            $table->string('twitter')->length(255)->nullable();
            $table->string('google_rank')->length(50)->nullable();

            $table->date('domain_registered')->nullable();
            $table->date('domain_expiry')->nullable();
            $table->string('domain_nameserver')->length(50)->nullable();
            $table->string('domain_registrar')->length(50)->nullable();
            $table->integer('instagram_followers')->length(50)->nullable();
            $table->integer('instagram_follows')->length(50)->nullable();
            $table->integer('instagram_total_photos')->length(50)->nullable();
            $table->integer('instagram_average_likes')->length(50)->nullable();

            $table->integer('instagram_average_comments')->length(50)->nullable();
            $table->char('instagram_is_verified')->length(3)->nullable();
            $table->integer('instagram_highlight_reel_count')->length(50)->nullable();
            $table->char('instagram_is_biz_account')->length(3)->nullable();
            $table->string('instagram_account_name')->length(50)->nullable();
            $table->char('yelp_ads')->length(3)->nullable();
            $table->char('fb_messenger_ads')->length(3)->nullable();
            $table->char('facebook_ads')->length(3)->nullable();

            $table->char('instagram_ads')->length(3)->nullable();
            $table->char('adwords_ads')->length(3)->nullable();
            $table->string('gmaps_url', )->length(255)->nullable();

            $table->string('gmb_claimed')->length(20)->nullable();

            $table->char('facebook_pixel')->length(3)->nullable();
            $table->char('google_pixel')->length(3)->nullable();
            $table->char('criteo_pixel')->length(3)->nullable();
            $table->double('google_stars', 8,2)->nullable();
            $table->integer('google_count')->length(50)->nullable();

            $table->double('yelp_stars', 50,2)->nullable();
            $table->integer('yelp_count')->length(50)->nullable();
            $table->double('facebook_stars', 50,2)->nullable();
            $table->integer('facebook_count')->length(50)->nullable();
            $table->string('main_category')->length(50)->nullable();
            $table->string('address')->length(255)->nullable();
            $table->string('city')->length(50)->nullable();
            $table->string('state')->length(50)->nullable();

            $table->string('zip')->length(50)->nullable();
            $table->char('mobile_friendly')->length(3)->nullable();
            $table->char('google_analytics')->length(3)->nullable();
            $table->char('schema_markup')->length(3)->nullable();
            $table->char('use_wordpress')->length(3)->nullable();
            $table->char('use_shopify')->length(3)->nullable();
            $table->char('linkedin_analytics')->length(3)->nullable();
            $table->char('status')->default(1);

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
