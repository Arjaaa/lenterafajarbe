<?php

namespace App\Traits;

trait ChecksTherapistPlacement
{
    /**
     * Cek apakah seorang therapist sudah dipakai sebagai Wali Kelas 2
     * di kelas lain, ATAU sudah jadi terapis di sesi 1on1 lain.
     */
    private function getTherapistPlacement(int $teacherId, ?string $exceptType = null, ?int $exceptId = null): ?string
    {
        $class = \App\Models\ClassRoom::where('homeroom_teacher_2_id', $teacherId)
            ->when($exceptType === 'class2' && $exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->first();
        if ($class) {
            return "wali kelas 2 di kelas {$class->name}";
        }

        $oneOnOne = \App\Models\OneOnOneGroup::where('teacher_id', $teacherId)
            ->when($exceptType === 'one_on_one' && $exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->first();
        if ($oneOnOne) {
            return "terapis di sesi 1 on 1 \"{$oneOnOne->name}\"";
        }

        return null;
    }
}