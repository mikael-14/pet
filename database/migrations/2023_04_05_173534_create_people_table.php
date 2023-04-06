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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->set('gender', ['male', 'female','undefined']);
            $table->string('email')->unique()->nullable();
            $table->string('phone',20)->nullable();
            $table->string('vat',20)->unique()->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address',200);
            $table->string('town',200);
            $table->text('observation')->nullable();
            $table->foreignId('users_id')->constrained()->nullable(); 
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
        Schema::dropIfExists('people');
    }
};
