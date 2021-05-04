<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('privacy')->default(false);
            //se nella relazione polimorfa in User gli passiamo queste colonne, le lasciamo così
            //altrimenti se non gliele passiamo dovremo chiamare le colonne col nome della relationship 
            //details_type e details_id
            // $table->string('profile_type')->nullable()->default(null);
            // $table->bigInteger('profile_id')->unsigned()->nullable()->default(null);
            $table->string('details_type')->nullable()->default(null);
            $table->bigInteger('details_id')->unsigned()->nullable()->default(null);
            $table->tinyInteger('role_id')->unsigned();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable()->default(null);
            $table->timestamp('last_login')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
