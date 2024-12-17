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
        Schema::create('token', function (Blueprint $table) {
            $table->id('id_token'); // SERIAL
            $table->string('valeur', 255)->nullable();
            $table->timestamp('date_creation')->nullable();
            $table->timestamp('date_expiration')->nullable();
            $table->unsignedBigInteger('id_utilisateur'); // Foreign key

            $table->foreign('id_utilisateur')
                ->references('id_utilisateur')
                ->on('utilisateur')
                ->onDelete('cascade'); // Optional: cascade delete

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
