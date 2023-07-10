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
        Schema::create('dewormings', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->set('type', ['internal', 'external', 'internal and external']);
            $table->integer('expire')->default(0)->comment('days of validation;0 not expiring');
            $table->integer('notification')->nullable()->comment('notification before/after expiration date');
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
        Schema::dropIfExists('dewormings');
    }
};
