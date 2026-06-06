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
        Schema::create('student_documentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->enum('media_type', ['photo', 'video']);
            $table->string('media_url');
            $table->string('thumbnail_url')->nullable(); // untuk video thumbnail
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('activity_date');
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('student_documentations');
    }
};
