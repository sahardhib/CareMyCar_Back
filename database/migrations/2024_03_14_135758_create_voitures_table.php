<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('voitures', function (Blueprint $table) {
           
            $table->id();// Add this line in the 'up' method
            $table->string('marque');
            $table->string('modele');
            $table->string('type');
            $table->string('matricule');
            $table->string('VIN');
            $table->string('image');
            $table->date('date_de_vignette'); // New date column for vignette date
            $table->date('date_d_assurance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voitures');
    }
};
