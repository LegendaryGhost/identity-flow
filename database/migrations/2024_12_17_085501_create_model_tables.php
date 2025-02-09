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
            $table->string('id', 255); // ID en string au lieu d'un auto-increment
            $table->primary('id'); // Définir explicitement la clé primaire

            $table->string('email', 75)->unique();
            $table->string('nom', 75);
            $table->string('prenom', 75);
            $table->string('mot_de_passe', 255);
            $table->date('date_naissance');
            $table->string('pdp', 255)->nullable();
            $table->integer('tentatives_connexion')->default(0);
        });

        Schema::create('token', function (Blueprint $table) {
            $table->id();
            $table->string('valeur', 255)->unique();
            $table->timestamp('date_heure_creation');
            $table->timestamp('date_heure_expiration')->nullable();
            $table->string('id_utilisateur', 255);

            $table->foreign('id_utilisateur')
                ->references('id')
                ->on('utilisateur')
                ->onDelete('cascade');
        });

        Schema::create('code_pin', function (Blueprint $table) {
            $table->id();
            $table->string('valeur', 6);
            $table->timestamp('date_heure_expiration')->nullable();
            $table->string('id_utilisateur', 255);

            $table->foreign('id_utilisateur')
                ->references('id')
                ->on('utilisateur')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateur');
        Schema::dropIfExists('token');
        Schema::dropIfExists('code_pin');
    }
};
