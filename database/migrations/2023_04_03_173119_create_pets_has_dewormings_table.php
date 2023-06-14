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
        Schema::create('pets_has_dewormings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pets_id')->constrained(); 
            $table->foreignId('dewormings_id')->constrained(); 
            $table->date('date');
            $table->date('expires_at')->nullable();
            $table->string('local',50)->nullable();
            $table->unsignedBigInteger('people_id')->nullable(); 
            $table->foreign('people_id')->references('id')->on('people');
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
