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
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('password')->nullable();
            $table->string('profile_image')->nullable();
            $table->integer('type')->default(1);
            $table->integer('coach_type')->nullable();
            $table->string('gender')->nullable();
            $table->string('age')->nullable();
            $table->string('height')->nullable();
            $table->string('position')->nullable();
            $table->string('country')->nullable();
            $table->string('country_logo')->nullable();
            $table->string('club')->nullable();
            $table->string('club_logo')->nullable();
            $table->string('bio')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('otp')->default(0);
            $table->integer('status')->default(0);
            $table->string('token')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
