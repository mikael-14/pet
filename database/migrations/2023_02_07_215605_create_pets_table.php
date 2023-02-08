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
            $table->string('image')->nullable();
            $table->string('chip',20)->nullable();
            $table->date('birth_date')->nullable();
            $table->date('entry_date');
            $table->string('sterilized')->nullable();
            $table->date('sterilized_date')->nullable();
            $table->float('weight',8,2,true)->nullable();
            $table->float('height',8,2,true)->nullable();
            $table->string('color',50)->nullable();
            $table->string('coat',50)->nullable();
            $table->string('observation',50)->nullable();
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