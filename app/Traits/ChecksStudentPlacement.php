<?php

namespace App\Traits;

trait ChecksStudentPlacement
{
    private function getStudentPlacement(int $studentId, ?string $exceptType = null, ?int $exceptId = null): ?string
    {
        $class = \App\Models\ClassRoom::whereHas('students', fn($q) => $q->where('students.id', $studentId))
            ->when($exceptType === 'class' && $exceptId, fn($q) => $q->where('classes.id', '!=', $exceptId))
            ->first();
        if ($class) {
            return "kelas {$class->name}";
        }

        $shadow = \App\Models\ShadowGroup::where('student_id', $studentId)
            ->when($exceptType === 'shadow' && $exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->first();
        if ($shadow) {
            return "group shadow \"{$shadow->name}\"";
        }

        $oneOnOne = \App\Models\OneOnOneGroup::where('student_id', $studentId)
            ->when($exceptType === 'one_on_one' && $exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->first();
        if ($oneOnOne) {
            return "sesi 1 on 1";
        }

        return null;
    }
}