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
        Schema::create('requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('subject');
            $table->foreignUlid('office_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUlid('category_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUlid('subcategory_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->smallInteger('priority')->nullable();
            $table->smallInteger('difficulty')->nullable();
            $table->datetime('availability')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
