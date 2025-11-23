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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('sex');
            $table->string('chip')->unique();
            $table->foreignId('animal_status_id')->nullable()->constrained()->nullOnDelete();

            // Specie and sub scpecie
            $table->foreignId('specie_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sub_specie_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('pelt_color_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('secondary_pelt_color_id')->nullable()->constrained('pelt_color','id')->nullOnDelete();
            $table->foreignId('pelt_schema_id')->nullable()->constrained()->nullOnDelete();

            $table->text('personnality');
            $table->dateTime('born_at');
            // Vaccine = intermediate table

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
