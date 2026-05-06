<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shadow_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                            
            $table->foreignId('student_id')                                 
                  ->constrained('students')
                  ->cascadeOnDelete();
            $table->foreignId('pic_id')                                    
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('partner_id')                                 
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('school_name');                                 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shadow_groups');
    }
};