<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_contact', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tag_id')->length(11);
            $table->unsignedBigInteger('contact_id')->length(11);
            $table->timestamps();

            $table->foreign('tag_id')
                  ->references('id')->on('tags')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreign('contact_id')
                  ->references('id')->on('contacts')
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
        Schema::dropIfExists('tag_contact');
    }
}
