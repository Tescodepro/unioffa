<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use App\Models\Campus;
use App\Models\UserType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StudentsImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected $errors = [];
    protected $successCount = 0;
    protected $skipCount = 0;

    public function collection(Collection $rows)
    {
        $userType = UserType::where('name', 'student')->firstOrFail();
        
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index
            
            try {
                // Clean and prepare data
                $data = $this->prepareRowData($row);
                
                // Validate row
                $validator = $this->validateRow($data, $rowNumber);
                
                if ($validator->fails()) {
                    $this->errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    $this->skipCount++;
                    continue;
                }
                
                // Check for duplicates
                if ($this->isDuplicate($data['email'], $data['phone'])) {
                    $this->errors[] = "Row {$rowNumber}: Email or phone number already exists";
                    $this->skipCount++;
                    continue;
                }
                
                // Get department
                $department = Department::where('department_code', strtoupper($data['department_code']))->first();
                
                if (!$department) {
                    $this->errors[] = "Row {$rowNumber}: Department code '{$data['department_code']}' not found";
                    $this->skipCount++;
                    continue;
                }
                
                // Get default campus (you can modify this logic)
                $campus = Campus::first();
                if (!$campus) {
                    $this->errors[] = "Row {$rowNumber}: No campus found in system";
                    $this->skipCount++;
                    continue;
                }
                
                // Process the student
                DB::beginTransaction();
                
                try {
                    // Extract admission year
                    $admissionYear = (int) explode('/', $data['admission_year'])[0];
                    
                    // Generate matric number
                    $matricNo = Student::generateMatricNo(
                        $department->department_code,
                        $admissionYear,
                        $data['entry_mode']
                    );
                    
                    // Create user
                    $user = User::create([
                        'first_name'       => $data['first_name'],
                        'last_name'        => $data['last_name'],
                        'email'            => $data['email'],
                        'phone'            => $data['phone'],
                        'username'         => $matricNo,
                        'registration_no'  => null,
                        'password'         => Hash::make($data['last_name']),
                        'user_type_id'     => $userType->id,
                        'date_of_birth'    => $data['dob'],
                        'campus_id'        => $campus->id,
                    ]);
                    
                    // Create student
                    Student::create([
                        'user_id'            => $user->id,
                        'campus_id'          => $campus->id,
                        'department_id'      => $department->id,
                        'matric_no'          => $matricNo,
                        'programme'          => $data['entry_mode'],
                        'stream'             => $data['stream'] ?? null,
                        'entry_mode'         => $data['entry_mode'],
                        'level'              => $data['level'],
                        'admission_session'  => $data['admission_year'],
                        'admission_date'     => "{$admissionYear}-09-01",
                        'status'             => 1,
                        'sex'                => $data['gender'],
                        'address'            => null,
                    ]);
                    
                    DB::commit();
                    $this->successCount++;
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->errors[] = "Row {$rowNumber}: Database error - " . $e->getMessage();
                    $this->skipCount++;
                }
                
            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                $this->skipCount++;
                continue;
            }
        }
        
        // Store results in session
        session()->flash('upload_success_count', $this->successCount);
        session()->flash('upload_skip_count', $this->skipCount);
        
        if (!empty($this->errors)) {
            session()->flash('upload_errors', $this->errors);
        }
    }
    
    protected function prepareRowData($row)
    {
        return [
            'first_name'      => trim($row['first_name'] ?? ''),
            'last_name'       => trim($row['last_name'] ?? ''),
            'email'           => strtolower(trim($row['email'] ?? '')),
            'phone'           => trim($row['phone'] ?? ''),
            'department_code' => strtoupper(trim($row['department_code'] ?? '')),
            'level'           => trim($row['level'] ?? ''),
            'gender'          => strtolower(trim($row['gender'] ?? '')),
            'admission_year'  => trim($row['admission_year'] ?? ''),
            'entry_mode'      => strtoupper(trim($row['entry_mode'] ?? '')),
            'dob'             => $row['dob'] ?? null,
            'stream'          => !empty($row['stream']) ? (int) $row['stream'] : null,
        ];
    }
    
    protected function validateRow(array $data, int $rowNumber)
    {
        return Validator::make($data, [
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'required|email',
            'phone'           => 'required|string',
            'department_code' => 'required|string',
            'level'           => 'required|integer|in:100,200,300,400,500',
            'gender'          => 'required|in:male,female',
            'admission_year'  => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'entry_mode'      => 'required|string|in:TOPUP,IDELUTME,IDELDE,UTME,TRANSFER,DIPLOMA,DE',
            'dob'             => 'required|date|before:today',
            'stream'          => 'nullable|integer',
        ]);
    }
    
    protected function isDuplicate($email, $phone)
    {
        return User::where('email', $email)
            ->orWhere('phone', $phone)
            ->exists();
    }
    
    public function batchSize(): int
    {
        return 100;
    }
    
    public function chunkSize(): int
    {
        return 100;
    }
}