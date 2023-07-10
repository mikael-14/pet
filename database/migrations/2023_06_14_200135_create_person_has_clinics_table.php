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
        Schema::create('person_has_clinics', function (Blueprint $table) {
            $table->foreignId('person_id')->constrained();
            $table->foreignId('clinic_id')->constrained();  
            // And finally, the indexes (Better perfs when fetching data on that pivot table)
            $table->index(['person_id', 'clinic_id'])->unique(); // This index has to be unique
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_has_clinics');
    }
};
