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
            $table->string('last_name', 25);
            $table->string('first_name', 25);
            $table->string('email', 150)->unique('user_email_unq');
            $table->string('password', 255);
            $table->string('phone_number', 15)->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_patient')->default(false);
            $table->boolean('is_specialist')->default(false);
            $table->boolean('is_guest')->default(true);
            $table->date('verified_at')->nullable();
            $table->string('verify_code', 255)->nullable();
            $table->rememberToken();
            $table->timestampsTz();
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
