<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('worksheets', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        
        $table->string('media')->nullable(); 
        
        $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
        $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
        $table->dateTime('deadline'); 
        $table->enum('status', ['belum_dikerjakan', 'selesai'])->default('belum_dikerjakan');
        $table->timestamp('submitted_at')->nullable(); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worksheets');
    }
};
