<?php

namespace App\Services;

use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Department;
use App\Models\Result;
use App\Models\Student;

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
     * @param  string|null  $semesterId  — pass null for sessional broadsheet
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

        // 1. Identify all unique courses registered or results uploaded for these students in this specific period
        // This will form the dynamic columns (e.g., ACC 103, ACC 201...)
        $registrationCodes = \App\Models\CourseRegistration::where('session', $session->name)
            ->when($semesterFilter, fn ($q) => $q->where('semester', $semesterFilter))
            ->whereIn('matric_no', $students->pluck('matric_no'))
            ->pluck('course_code')
            ->toArray();

        $resultCodes = \App\Models\Result::where('session', $session->name)
            ->when($semesterFilter, fn ($q) => $q->where('semester', $semesterFilter))
            ->whereIn('student_id', $students->pluck('user_id'))
            ->pluck('course_code')
            ->toArray();

        $courseCodes = collect(array_merge($registrationCodes, $resultCodes))
            ->unique()
            ->sort()
            ->values();

        // Get course details for the legend/key
        $coursesInfo = \App\Models\Course::whereIn('course_code', $courseCodes)->get()->keyBy('course_code');

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

        // Ordering for "Previous" calculation
        $allSessions = AcademicSession::orderBy('name', 'ASC')->pluck('name')->toArray();
        $allSemesters = ['1st', '2nd', '3nd']; // Defined in DB

        foreach ($students as $student) {
            $stats['total_students']++;

            // All registered courses (to find outstanding)
            $registrations = \App\Models\CourseRegistration::where('student_id', $student->user_id)
                ->where('session', $session->name)
                ->when($semesterFilter, fn ($q) => $q->where('semester', $semesterFilter))
                ->get();

            // All historical results
            $allResults = Result::where('student_id', $student->user_id)->get();

            // Results for the requested (Current) period
            $currentResults = $allResults->filter(function (Result $r) use ($session, $semesterFilter) {
                $match = $r->session === $session->name;
                if ($semesterFilter) {
                    $match = $match && $r->semester === $semesterFilter;
                }

                return $match;
            });

            // Outstanding: Registered but no result record with a total/grade
            $outstanding = $registrations->filter(function ($reg) use ($currentResults) {
                return ! $currentResults->contains('course_code', $reg->course_code);
            })->pluck('course_code')->toArray();

            // Current metrics
            $currentCalc = $this->calculateGPAFromResults($currentResults);

            // Previous metrics: everything strictly before current session/semester
            $previousResults = $allResults->filter(function (Result $r) use ($session, $semesterFilter, $allSessions, $allSemesters) {
                $sessIdxC = array_search($session->name, $allSessions);
                $sessIdxR = array_search($r->session, $allSessions);

                if ($sessIdxR < $sessIdxC) {
                    return true;
                }
                if ($sessIdxR > $sessIdxC) {
                    return false;
                }

                // Same session, compare semesters if we are filtering by semester
                if ($semesterFilter) {
                    $semIdxC = array_search($semesterFilter, $allSemesters);
                    $semIdxR = array_search($r->semester, $allSemesters);

                    return $semIdxR < $semIdxC;
                }

                return false;
            });
            $previousCalc = $this->calculateGPAFromResults($previousResults);

            // Cumulative: Current + Previous (or just all results filtered to current point)
            // Using all results up to AND including current
            $cumulativeResults = $allResults->filter(function (Result $r) use ($session, $semesterFilter, $allSessions, $allSemesters) {
                $sessIdxC = array_search($session->name, $allSessions);
                $sessIdxR = array_search($r->session, $allSessions);

                if ($sessIdxR < $sessIdxC) {
                    return true;
                }
                if ($sessIdxR > $sessIdxC) {
                    return false;
                }

                if ($semesterFilter) {
                    $semIdxC = array_search($semesterFilter, $allSemesters);
                    $semIdxR = array_search($r->semester, $allSemesters);

                    return $semIdxR <= $semIdxC;
                }

                return true;
            });
            $cumulativeCalc = $this->calculateGPAFromResults($cumulativeResults);

            // Results mapped to the dynamic course columns
            $courseResults = [];
            foreach ($courseCodes as $code) {
                $res = $currentResults->firstWhere('course_code', $code);
                $courseResults[$code] = $res ? ($res->total ?? '-') : '-';
            }

            // Stats booking
            if ($currentResults->contains(fn ($r) => ($r->total !== null && $r->total < 40) || $r->grade === 'F')) {
                $stats['repeats']++;
            } else {
                $stats['clear_passes']++;
            }

            $cgpa = $cumulativeCalc['gpa']; // calculateGPAFromResults returns 'gpa' as the ratio
            if ($cgpa >= 4.5) {
                $stats['cgpa_classes']['first_class']++;
            } elseif ($cgpa >= 1.0 && $cgpa <= 1.5) {
                $stats['cgpa_classes']['counselling']++;
            } elseif ($cgpa < 1.0 && $cgpa > 0) {
                $stats['cgpa_classes']['withdrawal']++;
            }

            $broadsheetData[] = [
                'student' => $student,
                'course_results' => $courseResults,
                'current' => $currentCalc,
                'previous' => $previousCalc,
                'cumulative' => $cumulativeCalc,
                'outstanding' => $outstanding,
                'academic_status' => $cgpa >= 1.5 ? 'GOOD STANDING' : ($cgpa > 0 ? 'PROBATION' : 'N/A'),
            ];
        }

        return [
            'department' => $department,
            'session' => $session,
            'semester' => $semester,
            'level' => $level,
            'course_codes' => $courseCodes,
            'courses_info' => $coursesInfo,
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
