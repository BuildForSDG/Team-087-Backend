<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialist_id')->constrained('specialists', 'user_id');
            $table->foreignId('patient_id')->constrained('patients', 'user_id');
            $table->string('remark', 160);
            $table->unsignedDecimal('rating', 3, 1)->default(0.0); //5.0
            $table->timestampsTz();

            $table->index(['specialist_id', 'patient_id', 'created_at'], 'fk_reviews_specialists_idx_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
