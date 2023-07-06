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
        Schema::create('prescription_has_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained();
            $table->foreignId('medicine_id')->constrained();
            $table->string('dosage', 50);
            $table->set('status', ['active', 'completed','on_hold','canceled']);
            $table->unsignedSmallInteger('frequency')->comment('time in hours beteween takes');//in hours
            $table->boolean('emergency')->default(false);
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('observation', 100);
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
        Schema::dropIfExists('prescription_medicines');
    }
};
