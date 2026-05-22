<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Restructure Gallery: was 1 row = 1 photo, now 1 row = 1 album
 * (folder) that holds many GalleryItem rows.
 *
 * Migration is data-preserving: every existing gallery becomes a
 * single-item album. The old media columns + the predefined-category
 * column get dropped — the album title IS the category now (user can
 * type any folder name).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) New items table — one row per photo/video inside an album.
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['image', 'video', 'youtube'])->default('image');
            $table->string('image_path', 500)->nullable();
            $table->string('video_path', 500)->nullable();
            $table->string('youtube_url', 500)->nullable();
            $table->json('caption')->nullable();                  // translatable
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->index(['gallery_id', 'sort_order']);
        });

        // 2) Album cover image (used by the public album-card view).
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('cover_image_path', 500)->nullable()->after('caption');
        });

        // 3) Migrate existing gallery rows → one item each, copying
        //    media into the new items table and using the existing
        //    image as the cover.
        $existing = DB::table('galleries')
            ->whereNotNull('image_path')
            ->orWhereNotNull('video_path')
            ->orWhereNotNull('youtube_url')
            ->get(['id', 'type', 'image_path', 'video_path', 'youtube_url', 'caption', 'is_published', 'created_at', 'updated_at']);

        foreach ($existing as $row) {
            DB::table('gallery_items')->insert([
                'gallery_id'   => $row->id,
                'type'         => $row->type,
                'image_path'   => $row->image_path,
                'video_path'   => $row->video_path,
                'youtube_url'  => $row->youtube_url,
                'caption'      => $row->caption,   // already JSON in source
                'sort_order'   => 0,
                'is_published' => $row->is_published,
                'created_at'   => $row->created_at,
                'updated_at'   => $row->updated_at,
            ]);
            if ($row->image_path) {
                DB::table('galleries')->where('id', $row->id)
                    ->update(['cover_image_path' => $row->image_path]);
            }
        }

        // 4) Drop old columns from galleries — albums don't carry
        //    media directly anymore. category goes too: the album
        //    title IS the category now.
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn(['type', 'image_path', 'video_path', 'youtube_url', 'category']);
        });
    }

    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('category', 50)->nullable();
            $table->enum('type', ['image', 'video', 'youtube'])->default('image');
            $table->string('image_path', 500)->nullable();
            $table->string('video_path', 500)->nullable();
            $table->string('youtube_url', 500)->nullable();
            $table->dropColumn('cover_image_path');
            $table->index('category');
        });

        Schema::dropIfExists('gallery_items');
    }
};
