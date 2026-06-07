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
        Schema::table('students', function (Blueprint $table) {
            // $table->string('photo')->nullable()->after('name');
            $table->string('father_name')->nullable()->after('school_name');
            $table->string('mother_name')->nullable()->after('father_name');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['photo', 'father_name', 'mother_name']);
        });
    }
};
