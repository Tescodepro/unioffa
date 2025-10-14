<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Return sample data rows
        return [
            [
                'John',
                'Doe',
                'john.doe@example.com',
                '08012345678',
                'CSC',
                '100',
                'male',
                '2024/2025',
                'UTME',
                '2005-05-15',
                ''
            ],
            [
                'Jane',
                'Smith',
                'jane.smith@example.com',
                '08087654321',
                'ENG',
                '200',
                'female',
                '2023/2024',
                'DE',
                '2004-08-20',
                '1'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'phone',
            'department_code',
            'level',
            'gender',
            'admission_year',
            'entry_mode',
            'dob',
            'stream'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 25,
            'D' => 15,
            'E' => 18,
            'F' => 10,
            'G' => 12,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 10,
        ];
    }
}