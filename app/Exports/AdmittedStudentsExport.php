<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AdmittedStudentsExport implements FromView, ShouldAutoSize
{
    protected $students;
    protected $filters;

    public function __construct($students, $filters = [])
    {
        $this->students = $students;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('exports.admitted-students', [
            'students' => $this->students,
            'filters' => $this->filters,
        ]);
    }
}
