<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\DailyReportClassification;

class ReportClassificationService
{
    // ── Mapping Kondisi Fisik ─────────────────────────────────────────────────

    private function classifyPhysicalCondition(?string $value): ?string
    {
        return match ($value) {
            'sehat'                   => 'positif',
            'sedikit_lelah'           => 'netral',
            'kurang_fit', 'mengantuk' => 'negatif',
            'lainnya'                 => null,
            default                   => null,
        };
    }

    private function classifyPhysicalEnergy(?string $value): ?string
    {
        return match ($value) {
            'ceria', 'aktif' => 'positif',
            'tenang'         => 'netral',
            'lelah'          => 'negatif',
            'lainnya'        => null, // akan dianalisa AI dari teks _other
            default          => null,
        };
    }

    // ── Mapping Mood ──────────────────────────────────────────────────────────

    private function classifyMood(?int $value): ?string
    {
        return match (true) {
            $value === 5 => 'sangat_baik',
            $value === 4 => 'baik',
            $value === 3 => 'cukup',
            $value === 2 => 'kurang',
            $value === 1 => 'sangat_kurang',
            default      => null,
        };
    }

    private function classifyMoodTrend(?int $arrival, ?int $end): ?string
    {
        if ($arrival === null || $end === null) return null;

        return match (true) {
            $end > $arrival => 'naik',
            $end < $arrival => 'turun',
            default         => 'stabil',
        };
    }

    // ── Mapping Perilaku ──────────────────────────────────────────────────────

    private function classifyBehavior(?string $value): ?string
    {
        return match ($value) {
            'kooperatif', 'fokus', 'aktif' => 'positif',
            'mudah_terdistraksi'            => 'negatif',
            'lainnya'                       => null, // akan dianalisa AI
            default                         => null,
        };
    }

    // ── Mapping Respon ────────────────────────────────────────────────────────

    private function classifyResponse(?string $value): ?string
    {
        return match ($value) {
            'antusias'                         => 'positif',
            'pasif'                            => 'netral',
            'perlu_arahan', 'perlu_pengawasan' => 'negatif',
            'lainnya'                          => null, // akan dianalisa AI
            default                            => null,
        };
    }
    private function classifyIndependence(?string $value): ?string
{
    return match ($value) {
        'sangat_mandiri' => 'sangat_mandiri',
        'mandiri'        => 'mandiri',
        'perlu_bantuan'  => 'perlu_bantuan',
        'lainnya'        => null,
        default          => null,
    };
}

    // ── Mapping Kendala ───────────────────────────────────────────────────────

    private function classifyChallenge(?string $value): ?string
    {
        return match ($value) {
            'kurang_fokus'       => 'ringan',
            'mudah_terdistraksi' => 'sedang',
            'mood_kurang_stabil' => 'sedang',
            'sulit_diarahkan'    => 'berat',
            'lainnya'            => null, // akan dianalisa AI
            default              => null,
        };
    }

    // ── Cek apakah ada field "lainnya" yang diisi ─────────────────────────────

private function hasOtherNote($detail): bool
{
    return !empty($detail->physical_condition_other)
        || !empty($detail->physical_condition_end_other)  // ganti dari physical_energy_other
        || !empty($detail->behavior_other)
        || !empty($detail->response_other)
        || !empty($detail->challenge_other);
}

    // ── Hitung Overall Score ──────────────────────────────────────────────────

    private function calculateOverallScore(array $categories): ?string
    {
        $scores = [];

        if ($categories['physical_condition_category']) {
            $scores[] = match ($categories['physical_condition_category']) {
                'positif' => 5, 'netral' => 3, 'negatif' => 1,
            };
        }

        if ($categories['physical_condition_end_category']) {
    $scores[] = match ($categories['physical_condition_end_category']) {
        'positif' => 5, 'netral' => 3, 'negatif' => 1,
          };
            }

        if ($categories['mood_arrival_category']) {
            $scores[] = match ($categories['mood_arrival_category']) {
                'sangat_baik' => 5, 'baik' => 4, 'cukup' => 3,
                'kurang' => 2, 'sangat_kurang' => 1,
            };
        }

        if ($categories['mood_end_category']) {
            $scores[] = match ($categories['mood_end_category']) {
                'sangat_baik' => 5, 'baik' => 4, 'cukup' => 3,
                'kurang' => 2, 'sangat_kurang' => 1,
            };
        }

        if ($categories['behavior_category']) {
            $scores[] = match ($categories['behavior_category']) {
                'positif' => 5, 'negatif' => 1, default => 3,
            };
        }

        if ($categories['response_category']) {
            $scores[] = match ($categories['response_category']) {
                'positif' => 5, 'netral' => 3, 'negatif' => 1, default => 3,
            };
        }

        if ($categories['has_challenge']) {
            $scores[] = match ($categories['challenge_category']) {
                'ringan' => 3, 'sedang' => 2, 'berat' => 1, default => 3,
            };
        }

        if (empty($scores)) return null;

        $avg = array_sum($scores) / count($scores);

        return match (true) {
            $avg >= 4.5 => 'sangat_baik',
            $avg >= 3.5 => 'baik',
            $avg >= 2.5 => 'cukup',
            $avg >= 1.5 => 'kurang',
            default     => 'sangat_kurang',
        };
    }

    // ── Main: classify & simpan ───────────────────────────────────────────────

   public function classify(DailyReport $report): DailyReportClassification
{
    $detail = $report->detail;

    $categories = [
        'physical_condition_category'      => $this->classifyPhysicalCondition($detail->physical_condition_arrival),
        'physical_condition_end_category'  => $this->classifyPhysicalCondition($detail->physical_condition_end),
        'physical_energy_arrival_category' => $this->classifyPhysicalEnergy($detail->physical_energy_arrival),
        'physical_energy_end_category'     => $this->classifyPhysicalEnergy($detail->physical_energy_end),
        'independence_category'            => $this->classifyIndependence($detail->independence),
        'mood_arrival_category'            => $this->classifyMood($detail->mood_arrival),
        'mood_end_category'                => $this->classifyMood($detail->mood_end),
        'mood_trend'                       => $this->classifyMoodTrend($detail->mood_arrival, $detail->mood_end),
        'behavior_category'                => $this->classifyBehavior($detail->behavior),
        'response_category'                => $this->classifyResponse($detail->response),
        'challenge_category'               => $this->classifyChallenge($detail->challenge),
        'has_challenge'                    => !is_null($detail->challenge),
        'has_homework'                     => (bool) $detail->has_homework,
        'has_other_note'                   => $this->hasOtherNote($detail),
    ];

    $categories['overall_score'] = $this->calculateOverallScore($categories);

    return DailyReportClassification::updateOrCreate(
        ['daily_report_id' => $report->id],
        $categories
    );
}
}