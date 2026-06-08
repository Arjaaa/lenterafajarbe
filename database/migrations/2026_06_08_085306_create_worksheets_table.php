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
        Schema::create('worksheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('file_url');
            $table->string('file_type'); // image, video, pdf, excel, word, other
            $table->string('original_filename');
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('worksheets');
    }
};
