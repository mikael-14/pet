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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('number',12)->unique();
            $table->foreignId('pet_id')->constrained();  
            $table->unsignedBigInteger('clinic_id')->nullable(); 
            $table->foreign('clinic_id')->references('id')->on('clinics');
            $table->foreignId('person_id')->constrained();  
            $table->date('date')->default(date('Y-m-d'));
            $table->string('observation', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prescriptions');
    }
};
