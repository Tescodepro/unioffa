<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\AcademicSemester;
use App\Models\Result;
use App\Models\Department;

class BroadsheetService
{
    /**
     * Convert grade letter to grade point
     */
    public function getGradePoint(string $grade): int
    {
        return match (strtoupper($grade)) {
            'A' => 5,
            'B' => 4,
            'C' => 3,
            'D' => 2,
            'E' => 1,
            default => 0,
        };
    }

    /**
     * Generate broadsheet data using only Results and Courses tables.
     *
     * @param  string       $departmentId
     * @param  string       $sessionId
     * @param  string       $level
     * @param  string|null  $semesterId   — pass null for sessional broadsheet
     */
    public function generateBroadsheet(string $departmentId, string $sessionId, string $level, ?string $semesterId = null): array
    {
        $session = AcademicSession::findOrFail($sessionId);
        $semester = $semesterId ? AcademicSemester::find($semesterId) : null;
        $department = Department::with('faculty')->findOrFail($departmentId);

        $semesterFilter = $semester ? $semester->code : null;

        // Students in this department + level
        $students = Student::with('user')
            ->where('department_id', $departmentId)
            ->where('level', $level)
            ->orderBy('matric_no')
            ->get();

        $broadsheetData = [];
        $stats = [
            'total_students' => 0,
            'clear_passes' => 0,
            'repeats' => 0,
            'cgpa_classes' => [
                'first_class' => 0,
                'counselling' => 0,
                'withdrawal' => 0,
            ],
        ];

        foreach ($students as $student) {
            $stats['total_students']++;

            // ── All historical results for CGPA ──────────────────────────────
            $allResults = Result::where('student_id', $student->user_id)->get();

            // ── Results for the requested period only ─────────────────────────
            $periodResults = $allResults->filter(function (Result $r) use ($session, $semesterFilter) {
                $match = $r->session === $session->name;
                if ($semesterFilter) {
                    $match = $match && $r->semester === $semesterFilter;
                }
                return $match;
            });

            // Group period results by semester for the table rows
            $resultsBySemester = $periodResults->groupBy('semester');

            // ── Period GPA ───────────────────────────────────────────────────
            $periodCalc = $this->calculateGPAFromResults($periodResults);

            // ── Cumulative CGPA ──────────────────────────────────────────────
            $cumulativeCalc = $this->calculateCGPAFromResults($allResults);

            // ── Stats bookkeeping ────────────────────────────────────────────
            $hasFailures = $periodResults->contains(fn($r) => $r->total !== null && $r->total < 40);
            if ($hasFailures) {
                $stats['repeats']++;
            } else {
                $stats['clear_passes']++;
            }

            $cgpa = $cumulativeCalc['cgpa'];
            if ($cgpa >= 4.5)
                $stats['cgpa_classes']['first_class']++;
            elseif ($cgpa >= 1.0 && $cgpa <= 1.5)
                $stats['cgpa_classes']['counselling']++;
            elseif ($cgpa < 1.0 && $cgpa > 0)
                $stats['cgpa_classes']['withdrawal']++;

            $broadsheetData[] = [
                'student' => $student,
                'results_by_semester' => $resultsBySemester,
                'period' => $periodCalc,
                'cumulative' => $cumulativeCalc,
            ];
        }

        return [
            'department' => $department,
            'session' => $session,
            'semester' => $semester,
            'level' => $level,
            'students_data' => $broadsheetData,
            'stats' => $stats,
        ];
    }

    /**
     * Calculate TCO, WGP, GPA from a collection of Result models.
     */
    private function calculateGPAFromResults($results): array
    {
        $tco = 0;
        $wgp = 0;

        foreach ($results as $result) {
            $unit = (int) $result->course_unit;
            $tco += $unit;
            if ($result->total !== null && $result->grade) {
                $wgp += $unit * $this->getGradePoint($result->grade);
            }
        }

        return [
            'tco' => $tco,
            'wgp' => $wgp,
            'gpa' => $tco > 0 ? round($wgp / $tco, 2) : 0,
        ];
    }

    /**
     * Calculate CTCO, CWGP, CGPA from all historical results.
     */
    private function calculateCGPAFromResults($allResults): array
    {
        $ctco = 0;
        $cwgp = 0;

        foreach ($allResults as $result) {
            $unit = (int) $result->course_unit;
            $ctco += $unit;
            if ($result->total !== null && $result->grade) {
                $cwgp += $unit * $this->getGradePoint($result->grade);
            }
        }

        return [
            'ctco' => $ctco,
            'cwgp' => $cwgp,
            'cgpa' => $ctco > 0 ? round($cwgp / $ctco, 2) : 0,
        ];
    }
}
