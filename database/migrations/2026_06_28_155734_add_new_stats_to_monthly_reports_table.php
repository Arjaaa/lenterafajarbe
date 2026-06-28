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
    Schema::table('monthly_reports', function (Blueprint $table) {
        $table->json('attendance_stats')->nullable()->after('total_challenges');
        $table->json('achievement_tag_stats')->nullable()->after('overall_score_stats');
        $table->json('communication_mode_stats')->nullable()->after('achievement_tag_stats');
        $table->json('communication_initiative_stats')->nullable()->after('communication_mode_stats');
        $table->json('social_with_teacher_stats')->nullable()->after('communication_initiative_stats');
        $table->json('social_with_peers_stats')->nullable()->after('social_with_teacher_stats');
    });
}

public function down(): void
{
    Schema::table('monthly_reports', function (Blueprint $table) {
        $table->dropColumn([
            'attendance_stats',
            'achievement_tag_stats',
            'communication_mode_stats',
            'communication_initiative_stats',
            'social_with_teacher_stats',
            'social_with_peers_stats',
        ]);
    });
}
};
