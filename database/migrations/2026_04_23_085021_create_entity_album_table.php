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
        Schema::create('entity_album', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
            $table->string('relationship_type')->default('featured'); // featured, dedicated, guest
            $table->timestamps();

            // Prevent duplicate relationships
            $table->unique(['entity_id', 'album_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_album');
    }
};
