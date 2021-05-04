<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_user', function (Blueprint $table) {
            //non serve
            // $table->bigIncrements('id');
            $table->bigInteger('skill_id');
            $table->bigInteger('user_id');
            //cifre totali, di cui dopo la virgola
            $table->decimal('level', 2, 1);
            $table->tinyInteger('experience_year');
            //non servono più, solo in prova
            // $table->timestamps();
            $table->primary(['skill_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_user');
    }
}
