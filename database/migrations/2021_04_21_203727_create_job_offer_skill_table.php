<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOfferSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_offer_skill', function (Blueprint $table) {
            // $table->bigIncrements('id');
            $table->bigInteger('job_offer_id');
            $table->bigInteger('skill_id');
            $table->decimal('min_level', 2, 1);
            $table->decimal('max_level', 2, 1);
            $table->tinyInteger('min_experience_years');
            //è strano il max_experience
            // $table->tinyInteger('max_experience_years');
            //non servono più, solo in prova
            // $table->timestamps();
            $table->primary(['job_offer_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_offer_skill');
    }
}
