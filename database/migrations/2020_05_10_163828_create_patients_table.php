<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->unsignedBigInteger('users_id')->primary();
            $table->string('card_no', 25);
            $table->char('blood_group', 3);
            $table->char('genotype', 3);
            $table->char('eye_colour', 15)->nullable();
            $table->char('skin_colour', 15)->nullable();
            $table->timestampsTz();

            $table->index('users_id', 'fk_patients_users_idx_1');
            $table->foreign('users_id', 'fk_patients_users')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
