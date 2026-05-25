<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['info', 'warning', 'urgent'])->default('info');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();    // null = tidak ada batas kadaluarsa
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Tambah school_name ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('school_name')->nullable()->default('Lentera Fajar Indonesia')->after('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('school_name');
        });
    }
};  