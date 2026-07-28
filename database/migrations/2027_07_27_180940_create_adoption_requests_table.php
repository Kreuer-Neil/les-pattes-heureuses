<?php

use App\Enums\PendingApprobationStatus;
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
        Schema::create('adoption_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('animal_id')->constrained();
            $table->foreignId('adopter_profile_id')->constrained();

            $table->enum('status', PendingApprobationStatus::cases())->default(PendingApprobationStatus::Unattended->value);

            $table->text('content');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_requests');
    }
};
