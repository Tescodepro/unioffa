<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FilteredStudentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $departmentId = $this->filters['department_id'] ?? null;
        $level = $this->filters['level'] ?? null;
        $name = $this->filters['name'] ?? null;
        $matric = $this->filters['matric_no'] ?? null;
        $phone = $this->filters['phone'] ?? null;
        $email = $this->filters['email'] ?? null;
        $campusId = $this->filters['campus_id'] ?? null;
        $stream = $this->filters['stream'] ?? null;

        return Student::query()->with(['user', 'department.faculty', 'campus'])
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->when($level, fn ($q) => $q->where('level', $level))
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId))
            ->when($stream, fn ($q) => $q->where('stream', $stream))
            ->when(
                $name,
                fn ($q) => $q->whereHas(
                    'user',
                    fn ($u) => $u->where('first_name', 'like', "%{$name}%")
                        ->orWhere('last_name', 'like', "%{$name}%")
                )
            )
            ->when($matric, fn ($q) => $q->where('matric_no', 'like', "%{$matric}%"))
            ->when(
                $phone,
                fn ($q) => $q->whereHas(
                    'user',
                    fn ($u) => $u->where('phone', 'like', "%{$phone}%")
                )
            )
            ->when(
                $email,
                fn ($q) => $q->whereHas(
                    'user',
                    fn ($u) => $u->where('email', 'like', "%{$email}%")
                )
            )
            ->orderBy('matric_no');
    }

    public function headings(): array
    {
        return [
            'Matric No',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Department',
            'Faculty',
            'Programme',
            'Level',
            'Campus',
            'Entry Mode',
            'Stream',
            'Sex',
        ];
    }

    public function map($student): array
    {
        return [
            $student->matric_no,
            $student->user->first_name ?? '',
            $student->user->last_name ?? '',
            $student->user->email ?? '',
            $student->user->phone ?? '',
            $student->department->department_name ?? '',
            $student->department->faculty->faculty_name ?? '',
            $student->programme,
            $student->level,
            $student->campus->name ?? '',
            $student->entry_mode,
            $student->stream,
            ucfirst($student->sex ?? ''),
        ];
    }
}
