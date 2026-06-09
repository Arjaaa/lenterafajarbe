<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_documentations', function (Blueprint $table) {
            $table->json('media_types')->nullable()->after('thumbnail_url');
        });
    }

    public function down(): void
    {
        Schema::table('student_documentations', function (Blueprint $table) {
            $table->dropColumn('media_types');
        });
    }
};