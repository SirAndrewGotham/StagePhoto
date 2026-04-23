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
        Schema::create('entity_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->string('locale', 2)->default('ru'); // ru, en, eo
            $table->string('name');
            $table->text('bio')->nullable();
            $table->text('story')->nullable(); // Extended narrative
            $table->string('website')->nullable();
            $table->json('social_links')->nullable(); // {telegram, vk, instagram, etc}
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable(); // For theaters
            $table->string('founded_year')->nullable(); // For bands/theaters
            $table->string('genre')->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_profiles');
    }
};
