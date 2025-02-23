<?php

use App\Enums\ActionStatus;
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
        Schema::create('actions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('request_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('user_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string('status')->nullable();
            $table->text('remarks')->nullable();
            $table->string('resolution')->nullable();
            $table->check("status = '" . ActionStatus::CLOSED->value . "' OR resolution IS NULL");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
