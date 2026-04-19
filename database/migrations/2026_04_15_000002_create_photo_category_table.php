<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('photo_category')) {
            Schema::create('photo_category', function (Blueprint $table) {
                $table->id();
                $table->uuid('photo_id');
                $table->foreignId('category_id')->constrained()->onDelete('cascade');
                $table->timestamps();

                $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
                $table->unique(['photo_id', 'category_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_category');
    }
};
