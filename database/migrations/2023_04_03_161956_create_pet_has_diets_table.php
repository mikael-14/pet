<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_has_diets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained(); 
            $table->foreignId('diet_id')->constrained(); 
            $table->date('date');
            $table->string('portion',50)->nullable();
            $table->string('observation',300)->nullable();
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
        Schema::dropIfExists('pets_has_diets');
    }
};
