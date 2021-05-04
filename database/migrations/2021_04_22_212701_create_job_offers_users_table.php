<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOffersUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_offers_users', function (Blueprint $table) {
            // $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('job_offer_id');
            $table->unique(['user_id', 'job_offer_id']);
            //non servono più, solo in prova
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_offers_users');
    }
}
