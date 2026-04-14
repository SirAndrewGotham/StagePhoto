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
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('locale', 2); // ru, en, eo
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['category_id', 'locale']);
        });

        // Pivot table for albums and categories
        Schema::create('album_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_category');
        Schema::dropIfExists('category_translations');
    }
};
