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
        Schema::create('tags', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('color', 24)->nullable();
            $table->foreignUlid('office_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('category_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('subcategory_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
