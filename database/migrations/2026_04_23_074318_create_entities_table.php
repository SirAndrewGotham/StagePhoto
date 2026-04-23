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
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->morphs('entityable'); // For theaters, bands, individuals
            $table->string('slug')->unique();
            $table->string('type'); // 'theater', 'band', 'individual'
            $table->boolean('is_published')->default(false);
            $table->json('settings')->nullable(); // Privacy settings, display preferences
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
