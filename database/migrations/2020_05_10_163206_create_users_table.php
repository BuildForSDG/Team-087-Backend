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
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->string('email', 150)->unique('user_email_unq');
            $table->string('password', 255);
            $table->string('phone_number', 15)->nullable();
            $table->string('photo')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'complicated'])->nullable();
            $table->boolean('is_patient')->default(false);
            $table->boolean('is_specialist')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_guest')->default(true);
            $table->boolean('is_active')->default(false);
            $table->date('profiled_at')->nullable();
            $table->string('profile_code', 255);
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
