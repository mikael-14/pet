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
            //$table->string('image')->nullable(); // no need if using spatie plugin
            $table->set('gender', ['male', 'female']);
            $table->boolean('adoptable');
            $table->string('chip',20)->nullable()->unique();
            $table->date('chip_date')->nullable();

            $table->unsignedBigInteger('entry_status_id')->nullable(); 
            $table->foreign('entry_status_id')->references('id')->on('statuses')->onDelete('set null');
            $table->date('entry_date');
            
            $table->foreignId('shelter_block_id')->constrained()->nullable(); 
            $table->unsignedBigInteger('status_id')->nullable(); 
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('set null');
            $table->date('status_date');

            $table->date('birth_date')->nullable();
            $table->boolean('sterilized');
            $table->date('sterilized_date')->nullable();
            $table->string('sterilized_local',50)->nullable();
            $table->string('color',50)->nullable();
            $table->string('coat',50)->nullable();
            $table->string('breed',50)->nullable();
            $table->string('qrcode', 32)->nullable()->unique();
            $table->text('observation')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('qrcode');
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
