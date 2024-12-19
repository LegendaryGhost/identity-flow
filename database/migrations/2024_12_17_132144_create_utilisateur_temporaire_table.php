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
        Schema::create('utilisateur_temporaire', function (Blueprint $table) {
            $table->id();
            $table->string('email', 75)->unique();
            $table->string('nom', 75);
            $table->string('prenom', 75);
            $table->string('mot_de_passe', 255);
            $table->date('date_naissance');
            $table->string('token_verification', 255);
            $table->timestamp('date_heure_creation')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateur_temporaire');
    }
};
