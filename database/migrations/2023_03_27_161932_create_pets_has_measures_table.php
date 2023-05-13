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
        Schema::create('pets_has_measures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pets_id')->constrained(); 
            $table->float('value',10,3,true);
            $table->string('type','20')->comment('defined in config/pet-measures'); //defined in config file
            $table->date('date');
            $table->string('local',50)->nullable();
            $table->foreignId('people_id')->constrained(); 
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
        Schema::dropIfExists('pets_has_measures');
    }
};
