<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->dropColumn([
                'achievement_note',
                'achievement_tag',
                'communication_mode',
                'communication_initiative',
                'social_with_teacher',
                'social_with_peers',
            ]);
        });

        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->dropColumn([
                'achievement_tag_stats',
                'communication_mode_stats',
                'communication_initiative_stats',
                'social_with_teacher_stats',
                'social_with_peers_stats',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->string('achievement_note', 500)->nullable()->after('homework_detail');
            $table->enum('achievement_tag', ['first_time', 'improvement', 'consistent'])->nullable()->after('achievement_note');
            $table->enum('communication_mode', ['verbal', 'non_verbal', 'gesture', 'aac'])->nullable()->after('achievement_tag');
            $table->enum('communication_initiative', ['often', 'sometimes', 'rarely'])->nullable()->after('communication_mode');
            $table->enum('social_with_teacher', ['responsive', 'needs_encouragement', 'refusing'])->nullable()->after('communication_initiative');
            $table->enum('social_with_peers', ['active', 'passive', 'avoiding'])->nullable()->after('social_with_teacher');
        });

        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->json('achievement_tag_stats')->nullable()->after('overall_score_stats');
            $table->json('communication_mode_stats')->nullable()->after('achievement_tag_stats');
            $table->json('communication_initiative_stats')->nullable()->after('communication_mode_stats');
            $table->json('social_with_teacher_stats')->nullable()->after('communication_initiative_stats');
            $table->json('social_with_peers_stats')->nullable()->after('social_with_teacher_stats');
        });
    }
};