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
        /**
         * Types of medication
         * Pills/Tablets: Solid forms of medication that are ingested orally. They are often round or oval-shaped and come in different sizes and colors.
         *
         * Capsules: Similar to pills, but they consist of medication enclosed in a gelatin shell. Capsules can be either hard or soft and are swallowed whole.
         *
         * Liquids/Syrups: Medications that are in a liquid form, often mixed with water or other solvents. They are usually measured using a dosing cup or spoon.
         *
         * Injections: Medications administered directly into the body through a needle and syringe. Injections can be given subcutaneously (under the skin), intramuscularly (into the muscle), or intravenously (into a vein).
         *
         * Topical Medications: Medications applied to the skin in the form of creams, ointments, gels, or patches. They are typically used for localized treatment.
         *
         * Inhalers: Devices that deliver medication in the form of a mist or spray directly into the lungs. Inhalers are commonly used for respiratory conditions.
         *
         * Suppositories: Solid medications in a cone or bullet shape that are inserted into the rectum or vagina for absorption.
         *
         * Drops: Liquid medications administered in small droplets, typically used for eye or ear conditions.
         *
         * Powders: Dry medications in powdered form that can be mixed with water or other liquids before administration.
         */
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->set('type', ['pills', 'capsule', 'liquid', 'injections', 'toppical', 'inhalers', 'suppositpries', 'drops', 'powder']);
            $table->string('dosage', 50)->nullable();
            $table->json('active_ingredient')->nullable();
            $table->string('aplication', 50)->nullable();
            $table->string('description', 500)->nullable();
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
        Schema::dropIfExists('medicines');
    }
};
