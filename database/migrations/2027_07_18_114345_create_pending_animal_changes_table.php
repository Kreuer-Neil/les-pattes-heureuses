<?php

use App\Enums\PendingChanges;
use App\Enums\PendingChangeStatus;
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
        Schema::create('pending_animal_changes', function (Blueprint $table) {
            $table->id();

            $table->enum('action', PendingChanges::cases());
            $table->enum('status', PendingChangeStatus::cases())->default(PendingChangeStatus::Pending->value);
            $table->foreignId('animal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Animal data/changes
            $table->json('payload');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_animal_changes');
    }
};
