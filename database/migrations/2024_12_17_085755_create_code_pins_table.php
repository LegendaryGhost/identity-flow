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
        Schema::create('code_pin', function (Blueprint $table) {
            $table->id(); // SERIAL
            $table->string('code', 6)->nullable();
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
        Schema::dropIfExists('code_pins');
    }
};
