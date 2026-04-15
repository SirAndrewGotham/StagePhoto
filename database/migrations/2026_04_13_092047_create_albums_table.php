<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();  // Make nullable
            $table->string('cover_image_square')->nullable();  // Make nullable
            $table->string('cover_image_hero')->nullable();  // Make nullable
            $table->foreignId('photographer_id')->constrained('users')->onDelete('cascade');
            $table->string('venue')->nullable();
            $table->date('event_date');
            $table->integer('photo_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('views')->default(0);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_unsorted')->default(false);
            $table->string('badge')->nullable();
            $table->string('badge_gradient')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('slug');
            $table->index('photographer_id');
            $table->index('is_published');
            $table->index('is_unsorted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
