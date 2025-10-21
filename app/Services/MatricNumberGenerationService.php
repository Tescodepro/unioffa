<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MatricNumberGenerationService
{
    /**
     * Generate matric number for student if needed
     *
     * @param Student $student
     * @return bool True if generated, False if already exists
     */
    public function generateIfNeeded(Student $student): bool
    {
        try {
            // ✅ CHECK IF NEEDS MATRIC (USING YOUR STATIC METHOD)
            if (Student::hasMatricNumber()) {
                Log::info("Student {$student->id} already has valid matric: {$student->matric_no}");
                return false; // Already has valid matric
            }

            $department = $student->department;
            if (!$department) {
                Log::error("No department found for student: {$student->id}");
                return false;
            }

            // Extract admission year
            $year = (int) Carbon::parse($student->admission_date)->year;
            
            // Generate new matric number
            $newMatricNo = Student::generateMatricNo(
                $department->department_code,
                $year,
                $student->entry_mode
            );

            // Wrap in DB transaction
            return DB::transaction(function () use ($student, $newMatricNo) {
                return $this->updateStudentAndUser($student, $newMatricNo);
            });

        } catch (\Exception $e) {
            Log::error('Matric number generation failed: ' . $e->getMessage(), [
                'student_id' => $student->id
            ]);
            return false;
        }
    }

    /**
     * Update student and user with new matric number
     */
    private function updateStudentAndUser(Student $student, string $newMatricNo): bool
    {
        $student->update(['matric_no' => $newMatricNo]);
        $student->user->update(['username' => $newMatricNo]);

        Log::info("Matric number generated successfully", [
            'student_id' => $student->id,
            'matric_no' => $newMatricNo
        ]);

        return true;
    }

    /**
     * Generate matric number for ALL students who need it
     *
     * @param string $userId
     * @return int Number of students updated
     */
    public function generateForUser(string $userId): int
    {
        $students = Student::where('user_id', $userId)
            ->whereNull('matric_no')
            ->orWhereRaw("matric_no LIKE 'UOO/APP/%'")
            ->get();

        $updated = 0;
        foreach ($students as $student) {
            if ($this->generateIfNeeded($student)) {
                $updated++;
            }
        }

        return $updated;
    }
}