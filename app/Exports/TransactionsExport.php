<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    protected $rowNumber = 0;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            '#',
            'Reference',
            'Student',
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

        return [
            $this->rowNumber,
            $transaction->refernce_number,
            ($transaction->user->first_name ?? '').' '.($transaction->user->last_name ?? ''),
            ucfirst($transaction->payment_type),
            number_format($transaction->amount, 2),
            $status,
            $transaction->created_at->format('d M, Y h:i A'),
        ];
    }
}
