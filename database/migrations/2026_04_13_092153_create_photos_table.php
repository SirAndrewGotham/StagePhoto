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
        Schema::create('photos', function (Blueprint $table) {
            $table->uuid('id')->primary();  // Change this line
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
            $table->string('filename')->nullable();
            $table->string('path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->integer('views')->default(0);
            $table->string('title')->nullable();
            $table->string('original_path')->nullable();
            $table->string('full_path')->nullable();
            $table->string('hash', 64)->nullable();  // Remove ->unique() for SQLite compatibility
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('hash');
            $table->index('sort_order');
            $table->index('album_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
