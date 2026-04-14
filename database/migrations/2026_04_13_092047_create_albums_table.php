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
        Schema::create('albums', function (Blueprint $table) {
            //            $table->id();
            //            $table->foreignId('team_id')->nullable()->after('photographer_id')->constrained()->nullOnDelete();
            //            $table->foreignId('photographer_id')->constrained('users')->cascadeOnDelete();
            //            $table->string('title');
            //            $table->string('slug')->unique();
            //            $table->date('event_date');
            //            $table->string('venue')->nullable();
            //            $table->string('city')->nullable();
            //            $table->text('description')->nullable();
            //            $table->boolean('is_featured')->default(false);
            //            $table->boolean('is_published')->default(true);
            //            $table->unsignedInteger('views_count')->default(0);
            //            $table->decimal('avg_rating', 3, 2)->default(0);
            //            $table->timestamps();
            //            $table->index(['is_published', 'event_date']);
            //            $table->index('photographer_id');

            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image');
            $table->foreignId('photographer_id')->constrained('users')->onDelete('cascade');
            $table->string('venue')->nullable();
            $table->date('event_date');
            $table->integer('photo_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('views')->default(0);
            $table->boolean('is_published')->default(true);
            $table->string('badge')->nullable();
            $table->string('badge_gradient')->nullable();
            $table->timestamps();
        });

        // Pivot table for albums and categories (already created earlier)
        // If not created yet:
        if (! Schema::hasTable('album_category')) {
            Schema::create('album_category', function (Blueprint $table) {
                $table->id();
                $table->foreignId('album_id')->constrained()->onDelete('cascade');
                $table->foreignId('category_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
