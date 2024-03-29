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
        Schema::create('shelter_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelter_id')->constrained();
            $table->string('name',50);
            $table->string('color','7')->default('#cccccc')->nullable();
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
        Schema::dropIfExists('shelter_blocks');
    }
};
