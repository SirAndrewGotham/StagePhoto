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

            // exif
            $table->json('exif_data')->nullable()->after('description');
            $table->string('camera_make')->nullable()->after('exif_data');
            $table->string('camera_model')->nullable()->after('camera_make');
            $table->string('lens_model')->nullable()->after('camera_model');
            $table->string('focal_length')->nullable()->after('lens_model');
            $table->string('aperture')->nullable()->after('focal_length');
            $table->string('shutter_speed')->nullable()->after('aperture');
            $table->string('iso')->nullable()->after('shutter_speed');
            $table->timestamp('captured_at')->nullable()->after('iso');
            $table->string('gps_latitude')->nullable()->after('captured_at');
            $table->string('gps_longitude')->nullable()->after('gps_latitude');

            // statuses
            $table->string('status')->default('pending')->after('is_featured');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            // timestamps
            $table->timestamps();
            $table->softDeletes();

            // indexes
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
