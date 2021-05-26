<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('bio');
            $table->date('birth_date')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->string('gender')->nullable()->default(null);
            $table->string('city');
            $table->text('logo');
            //avendo creato la relazione polimorfa e messo l'id dei details nella tabella User non ho più bisogno di questa
            // $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_detail');
    }
}
