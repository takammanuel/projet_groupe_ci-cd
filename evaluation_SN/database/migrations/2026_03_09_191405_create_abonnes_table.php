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
       Schema::create('abonnes', function (Blueprint $table) {
    $table->id();
    $table->string('nom');
    $table->string('prenom');
    $table->enum('ville',['Yaounde','Douala','Bafoussam','Garoua']);
    $table->string('quartier')->nullable();
    $table->string('numeroCompteur')->unique();
    $table->enum('typeAbonnement',['Domestique','Professionnel']);
    $table->timestamp('dateCreation')->useCurrent();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnes');
    }
};
