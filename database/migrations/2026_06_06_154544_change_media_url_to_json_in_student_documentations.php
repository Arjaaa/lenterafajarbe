<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing string values ke JSON array sebelum ubah kolom
        DB::table('student_documentations')->get()->each(function ($row) {
            DB::table('student_documentations')->where('id', $row->id)->update([
                'media_url'     => $row->media_url     ? json_encode([$row->media_url])     : json_encode([]),
                'thumbnail_url' => $row->thumbnail_url ? json_encode([$row->thumbnail_url]) : json_encode([]),
            ]);
        });

        Schema::table('student_documentations', function (Blueprint $table) {
            $table->json('media_url')->nullable()->change();
            $table->json('thumbnail_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('student_documentations')->get()->each(function ($row) {
            $mediaUrls     = json_decode($row->media_url, true);
            $thumbnailUrls = json_decode($row->thumbnail_url, true);

            DB::table('student_documentations')->where('id', $row->id)->update([
                'media_url'     => $mediaUrls[0]     ?? null,
                'thumbnail_url' => $thumbnailUrls[0] ?? null,
            ]);
        });

        Schema::table('student_documentations', function (Blueprint $table) {
            $table->string('media_url')->nullable()->change();
            $table->string('thumbnail_url')->nullable()->change();
        });
    }
};