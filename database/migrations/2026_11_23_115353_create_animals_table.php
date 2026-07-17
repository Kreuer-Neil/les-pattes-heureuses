<?php

use App\Enums\Animals\Gender;
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
            $table->enum('gender', Gender::cases());
            $table->string('chip')->unique();
            $table->foreignId('animal_status_id')->constrained('animal_statuses');

            $table->foreignId('specie_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('fur_color_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('secondary_fur_color_id')->nullable()->constrained('fur_colors','id')->nullOnDelete();
            $table->foreignId('fur_pattern_id')->nullable()->constrained()->nullOnDelete();

            $table->text('personality');
            $table->date('born_at'); // Could be nullable? Date could be unknown

            $table->index(['animal_status_id','specie_id']);

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
