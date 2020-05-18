<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialists', function (Blueprint $table) {
            $table->unsignedBigInteger('users_id')->primary();
            $table->string('license_no', 25);
            $table->date('licensed_at');
            $table->date('last_renewed_at');
            $table->date('expires_at');
            $table->boolean('is_verified')->default(false);
            $table->timestampsTz();

            $table->index('users_id', 'fk_patients_users_idx_2');
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
        Schema::dropIfExists('specialists');
    }
}
