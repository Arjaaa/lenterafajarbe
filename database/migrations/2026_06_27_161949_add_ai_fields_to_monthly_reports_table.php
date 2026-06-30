<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->string('ai_headline', 100)->nullable()->after('ai_recommendation');
            $table->string('ai_headline_emoji', 10)->nullable()->after('ai_headline');
            $table->enum('ai_attention_trend', ['improving', 'stable', 'worsening'])->nullable()->after('ai_headline_emoji');
            $table->string('ai_attention_note', 200)->nullable()->after('ai_attention_trend');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->dropColumn(['ai_headline', 'ai_headline_emoji', 'ai_attention_trend', 'ai_attention_note']);
        });
    }
};