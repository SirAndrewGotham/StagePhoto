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
        Schema::create('entity_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->uuid('photo_id'); // Because photos uses UUID as primary key
            $table->timestamps();

            // Add foreign key for photo_id (since photos uses UUID)
            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');

            // Prevent duplicate tags
            $table->unique(['entity_id', 'photo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_photos');
    }
};
