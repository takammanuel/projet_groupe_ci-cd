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
       Schema::create('factures', function (Blueprint $table) {
    $table->id();
    $table->foreignId('abonne_id')->constrained('abonnes')->cascadeOnDelete();
    $table->integer('consommation');
    $table->integer('montantTotal');
    $table->date('dateEmission');
    $table->enum('statut',['Emise','Payee']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
