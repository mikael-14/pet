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
            $table->string('cc',30)->unique()->nullable();
            $table->date('birth_date')->nullable();
            $table->string('country',100)->nullable(); //país
            $table->string('state',100)->nullable(); //district/estado
            $table->string('local',100)->nullable(); //local
            $table->string('street',100)->nullable(); //rua
            $table->string('zip',20)->nullable();
            $table->decimal('latitude',11,8);
            $table->decimal('longitude',11,8);
            $table->text('observation')->nullable();
            $table->unsignedBigInteger('users_id')->nullable(); 
            $table->foreign('users_id')->references('id')->on('users')->onDelete('set null');
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
