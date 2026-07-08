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
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', [
            'coordinator_main',
            'coordinator_therapist',
            'coordinator_shadow',
            'coordinator_wil',
            'shadow_pj',
            'shadow_teacher',
            'therapist_homeroom',
            'therapist',
            'parent',
        ])->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', [
            'coordinator_main',
            'coordinator_therapist',
            'coordinator_shadow',
            'coordinator_wil',
            'shadow_pj',
            'shadow_teacher',
            'therapist_homeroom',
            'therapist',
            'parent',
        ])->nullable(false)->change();
    });
}
};
