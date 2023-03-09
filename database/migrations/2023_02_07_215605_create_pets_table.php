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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('species',20)->nullable();
            $table->string('image')->nullable();
            $table->set('gender', ['male', 'female']);
            $table->string('chip',20)->nullable()->unique();
            $table->date('chip_date')->nullable();
            $table->foreignId('pet_statuses_id')->constrained()->nullable(); 
            $table->foreignId('pet_locations_id')->constrained()->nullable(); 
            $table->date('birth_date')->nullable();
            $table->date('entry_date');
            $table->boolean('sterilized');
            $table->date('sterilized_date')->nullable();
            $table->string('sterilized_local',50)->nullable();
            $table->string('color',50)->nullable();
            $table->string('coat',50)->nullable();
            $table->text('observation')->nullable();
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
        Schema::dropIfExists('pets');
    }
};
