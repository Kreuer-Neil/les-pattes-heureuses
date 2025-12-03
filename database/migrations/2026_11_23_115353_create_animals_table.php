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
            $table->string('image')->nullable();
            $table->string('gender');
            $table->string('chip')->unique();
            $table->string('animal_status')->default(1);

            // Specie and sub scpecie
            $table->foreignId('specie_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('fur_color_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('secondary_fur_color_id')->nullable()->constrained('fur_colors','id')->nullOnDelete();
            $table->foreignId('fur_pattern_id')->nullable()->constrained()->nullOnDelete();

            $table->text('personality');
            $table->date('born_at');
            // Vaccine = intermediate table
            // Same goes for the notes

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
