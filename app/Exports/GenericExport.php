<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Controllers\Staff\BursaryController;
use Illuminate\Support\Collection;

class GenericExport implements FromCollection
{
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function collection()
    {
        $controller = app(BursaryController::class);
        $rows = [];

        switch ($this->type) {
            case 'faculty':
                // Get the data from the controller method (which returns a View)
                $view = $controller->reportByFaculty(request());
                $data = $view->getData()['data'] ?? collect();

                // Add header row
                $rows[] = ['Faculty', 'Total Transactions', 'Expected Amount (₦)', 'Received Amount (₦)', 'Outstanding (₦)'];

                foreach ($data as $item) {
                    $rows[] = [
                        $item['faculty'],
                        $item['total_transactions'],
                        $item['expected'],
                        $item['received'],
                        $item['outstanding'],
                    ];
                }
                break;

            case 'department':
                $view = $controller->reportByDepartment(request());
                $data = $view->getData()['data'] ?? collect();

                $rows[] = ['Faculty', 'Department', 'Total Transactions', 'Expected Amount (₦)', 'Received Amount (₦)', 'Outstanding (₦)'];

                foreach ($data as $item) {
                    $rows[] = [
                        $item['faculty'],
                        $item['department'],
                        $item['total_transactions'],
                        $item['expected'],
                        $item['received'],
                        $item['outstanding'],
                    ];
                }
                break;

            case 'level':
                $view = $controller->reportByLevel(request());
                $data = $view->getData()['data'] ?? collect();

                $rows[] = ['Level', 'Expected Amount (₦)', 'Received Amount (₦)', 'Outstanding (₦)'];

                foreach ($data as $item) {
                    $rows[] = [
                        $item['level'],
                        $item['expected'],
                        $item['received'],
                        $item['outstanding'],
                    ];
                }
                break;

            case 'student':
                $view = $controller->reportByStudent(request());
                $data = $view->getData()['data'] ?? collect();

                $rows[] = ['Student Name', 'Matric Number', 'Level', 'Entry Mode', 'Center', 'Faculty', 'Department', 'Expected Amount (₦)', 'Received Amount (₦)', 'Outstanding (₦)'];

                foreach ($data as $item) {
                    $rows[] = [
                        $item['student_name'],
                        $item['matric_number'],
                        $item['level'],
                        $item['entry_mode'],
                        $item['center'],
                        $item['faculty'],
                        $item['department'],
                        $item['expected'],
                        $item['received'],
                        $item['outstanding'],
                    ];
                }
                break;
        }

        return collect($rows);
    }
}

