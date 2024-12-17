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
        Schema::create('utilisateur', function (Blueprint $table) {
            $table->id('id_utilisateur'); // SERIAL
            $table->string('email', 50)->unique(); // UNIQUE
            $table->string('nom', 50)->nullable();
            $table->string('prenom', 50)->nullable();
            $table->string('mot_de_passe', 50); // NOT NULL
            $table->date('date_naissance');
            $table->timestamps(); // Adds created_at and updated_at
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
