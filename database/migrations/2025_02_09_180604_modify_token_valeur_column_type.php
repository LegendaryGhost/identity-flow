<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('token', function (Blueprint $table) {
            $table->text('valeur')->change();
        });
    }

    public function down(): void
    {
        Schema::table('token', function (Blueprint $table) {
            $table->string('valeur', 255)->change();
        });
    }
};
