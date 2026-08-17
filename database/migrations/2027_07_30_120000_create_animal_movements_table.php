<?php

use App\Enums\Animals\MovementType;
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
        Schema::create('animal_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('animal_id')->constrained()->cascadeOnDelete();
            $table->enum('type', MovementType::cases());
            $table->date('occurred_at');

            $table->index('animal_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_movements');
    }
};