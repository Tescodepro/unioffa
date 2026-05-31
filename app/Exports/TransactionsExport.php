<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $query;

    protected $rowNumber = 0;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->latest();
    }

    public function headings(): array
    {
        return [
            '#',
            'Reference',
            'Student',
            'Matric Number',
            'Center',
            'Department',
            'Level',
            'Entry Mode',
            'Payment Type',
            'Amount (₦)',
            'Status',
            'Date',
        ];
    }

    public function map($transaction): array
    {
        $this->rowNumber++;

        $status = 'Pending';
        if ($transaction->payment_status == 1) {
            $status = 'Successful';
        } elseif ($transaction->payment_status == 2) {
            $status = 'Failed';
        }

        $profile = $transaction->user->studentProfile ?? null;

        return [
            $this->rowNumber,
            $transaction->refernce_number,
            trim(($transaction->user->first_name ?? '').' '.($transaction->user->last_name ?? '')),
            $profile->matric_no ?? '—',
            $transaction->user->campus->name ?? '—',
            $profile->department->department_name ?? '—',
            $profile->level ?? '—',
            $profile->entry_mode ?? optional($transaction->user->applicationSetting)->application_code ?? '—',
            ucfirst($transaction->payment_type),
            number_format($transaction->amount, 2),
            $status,
            $transaction->created_at->format('d M, Y h:i A'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
