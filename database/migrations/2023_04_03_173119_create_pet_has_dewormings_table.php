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
        Schema::create('pet_has_dewormings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained(); 
            $table->foreignId('deworming_id')->constrained(); 
            $table->date('date');
            $table->date('expire_at')->nullable();
            $table->string('local',50)->nullable();
            $table->unsignedBigInteger('person_id')->nullable(); 
            $table->foreign('person_id')->references('id')->on('people');
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
        Schema::dropIfExists('pets_has_dewormings');
    }
};
