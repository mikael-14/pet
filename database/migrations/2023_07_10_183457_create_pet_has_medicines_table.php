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
        Schema::create('pet_has_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained();
            $table->foreignId('medicine_id')->constrained();
            $table->string('dosage', 50);
            $table->set('status', ['active', 'completed','on_hold','canceled'])->nullable();
            $table->boolean('emergency')->default(false);
            $table->boolean('administered')->nullable();
            $table->dateTime('date');
            $table->string('observation', 200)->nullable();
            $table->unsignedBigInteger('person_id')->nullable(); 
            $table->foreign('person_id')->references('id')->on('people');
            $table->unsignedBigInteger('prescription_has_medicine_id')->nullable(); 
            $table->foreign('prescription_has_medicine_id')->references('id')->on('prescription_has_medicines');
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
        Schema::dropIfExists('pet_has_medicines');
    }
};
