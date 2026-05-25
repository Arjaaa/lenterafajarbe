<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('name');
            $table->date('birth_date')->nullable()->after('photo');
            $table->enum('gender', ['laki-laki', 'perempuan'])->nullable()->after('birth_date');
            $table->string('school_name')->nullable()->after('gender');      
            $table->text('address')->nullable()->after('school_name');        
            $table->enum('special_needs', [
                'autis',
                'adhd',
                'down_syndrome',
                'lambat_belajar',
                'tunarungu',
                'tunawicara',
                'tunagrahita',
                'lainnya',
            ])->nullable()->after('address');
            $table->text('diagnosis_notes')->nullable()->after('special_needs'); 
            $table->foreignId('parent_id')                                   
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('diagnosis_notes');
            $table->string('parent_phone')->nullable()->after('parent_id');  
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'photo', 'birth_date', 'gender', 'school_name',
                'address', 'special_needs', 'diagnosis_notes',
                'parent_id', 'parent_phone',
            ]);
        });
    }
};