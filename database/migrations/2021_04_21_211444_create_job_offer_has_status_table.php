<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOfferHasStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_offer_has_status', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedBigInteger('job_offer_id');
            // $table->bigInteger('job_offer_id')->unsigned();
            $table->unsignedTinyInteger('job_offer_status_id');
            $table->boolean('last')->default(true);
            $table->timestamp('from');
            $table->timestamp('to')->nullable()->default(null);
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
        Schema::dropIfExists('job_offer_has_status');
    }
}
