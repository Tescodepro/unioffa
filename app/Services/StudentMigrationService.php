<?php

namespace App\Services;

use App\Models\Student;
use App\Models\UserApplications;
use App\Models\ApplicationSetting;
use App\Models\AdmissionList;
use App\Models\UserType;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentMigrationService
{
    /**
     * Migrate user to student
     *
     * @param string|null $userId
     * @return Student|null
     */
    public function migrate(string | null $userId = null): ?Student
    {
        try {
            $user = $userId ? User::findOrFail($userId) : Auth::user();

            if (!$user) {
                Log::error('User not found for migration: ' . $userId);
                return null;
            }

            // ✅ PREVENT DUPLICATES - CHECK IF ALREADY STUDENT
            if ($user->student) {
                Log::info("User {$user->id} is already a student: {$user->student->id}");
                return $user->student; // RETURN EXISTING STUDENT
            }

            // Wrap entire migration in DB transaction
            return DB::transaction(function () use ($user) {
                return $this->performMigration($user);
            });

        } catch (\Exception $e) {
            Log::error('Student migration failed: ' . $e->getMessage(), [
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Perform the actual migration logic
     */
    private function performMigration(User $user): Student
    {
        // Update user type to student
        $this->updateUserType($user);

        // Get current session
        $currentSession = $this->getActiveSession();

        // Get user application
        $userApplication = $this->getUserApplication($user->id, $currentSession);

        // Get application settings
        $applicationSetting = $this->getApplicationSetting($userApplication);

        // Get admission
        $admission = $this->getAdmission($user->id);

        // Prepare student data based on application type
        $studentData = $this->prepareStudentData($applicationSetting, $userApplication, $user);

        // Create student record
        return $this->createStudent($user, $admission, $studentData);
    }

    /**
     * Update user type to student
     */
    private function updateUserType(User $user): void
    {
        $studentType = UserType::where('name', 'student')->first();
        if ($studentType) {
            $user->update(['user_type_id' => $studentType->id]);
        }
    }

    /**
     * Get active session
     */
    private function getActiveSession(): ?string
    {
        return activeSession()->name ?? null;
    }

    /**
     * Get user application
     */
    private function getUserApplication(string $userId, ?string $session): ?UserApplications
    {
        return UserApplications::where('user_id', $userId)
            ->where('academic_session', $session)
            ->first();
    }

    /**
     * Get application setting
     */
    private function getApplicationSetting(UserApplications $userApplication): ?ApplicationSetting
    {
        return ApplicationSetting::find($userApplication->application_setting_id);
    }

    /**
     * Get admission record
     */
    private function getAdmission(string $userId): ?AdmissionList
    {
        return AdmissionList::where('user_id', $userId)->first();
    }

    /**
     * Prepare student data based on application type
     */
    private function prepareStudentData(
        ApplicationSetting $applicationSetting,
        UserApplications $userApplication,
        User $user
    ): array {
        $applicationCode = $applicationSetting->application_code;

        return match ($applicationCode) {
            'DE' => [
                'programme' => 'REGULAR',
                'entry_mode' => 'DE',
                'level' => '200',
                'admission_session' => $userApplication->academic_session,
                'sex' => $user->gender,
            ],
            'TOPUP' => [
                'programme' => 'TOPUP',
                'entry_mode' => 'TOPUP',
                'level' => '200',
                'admission_session' => $userApplication->academic_session,
            ],
            'TRANSFER' => [
                'programme' => 'REGULAR',
                'entry_mode' => 'TRANSFER',
                'level' => '200',
                'admission_session' => $userApplication->academic_session,
            ],
            'IDELUTME' => [
                'programme' => 'IDELUTME',
                'entry_mode' => 'UTME',
                'level' => '100',
                'admission_session' => $userApplication->academic_session,
            ],
            'IDELDE' => [
                'programme' => 'IDELDE',
                'entry_mode' => 'DE',
                'level' => '200',
                'admission_session' => $userApplication->academic_session,
            ],
            'UTME' => [
                'programme' => 'REGULAR',
                'entry_mode' => 'UTME',
                'level' => '100',
                'admission_session' => $userApplication->academic_session,
            ],
            default => [
                'programme' => 'REGULAR',
                'entry_mode' => 'UTME',
                'level' => '100',
                'admission_session' => $userApplication->academic_session,
            ]
        };
    }

    /**
     * Create student record
     */
    private function createStudent(User $user, AdmissionList $admission, array $studentData): Student
    {
        $student = Student::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'campus_id' => $user->campus_id,
            'department_id' => $admission->approved_department_id,
            'matric_no' => $user->registration_no,
            'programme' => $studentData['programme'],
            'entry_mode' => $studentData['entry_mode'],
            'level' => $studentData['level'],
            'admission_session' => $studentData['admission_session'],
            'admission_date' => now(),
            'status' => 1,
            'sex' => $user->sex ?? $studentData['sex'] ?? null,
        ]);

        Log::info("Student migrated successfully", [
            'student_id' => $student->id,
            'user_id' => $user->id,
            'matric_no' => $student->matric_no
        ]);

        return $student;
    }
}
