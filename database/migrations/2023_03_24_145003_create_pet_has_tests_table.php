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
        Schema::create('pet_has_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained(); 
            $table->foreignId('test_id')->constrained(); 
            $table->date('date');
            $table->set('result',['unknown','positive','negative']);
            $table->string('local',50)->nullable();
            $table->unsignedBigInteger('person_id')->nullable(); 
            $table->foreign('person_id')->references('id')->on('people');
            $table->string('observation',300)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('pets_has_tests');
    }
};
