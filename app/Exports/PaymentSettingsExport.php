<?php

namespace App\Exports;

use App\Models\PaymentSetting;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentSettingsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = PaymentSetting::query()->orderBy('created_at', 'desc');

        if ($this->request->filled('payment_type')) {
            $query->where('payment_type', $this->request->payment_type);
        }
        if ($this->request->filled('session')) {
            $query->where('session', $this->request->session);
        }
        if ($this->request->filled('faculty_id')) {
            $query->whereJsonContains('faculty_ids', $this->request->faculty_id);
        }
        if ($this->request->filled('department_id')) {
            $query->whereJsonContains('department_ids', $this->request->department_id);
        }
        if ($this->request->filled('student_type')) {
            $query->whereJsonContains('student_type', $this->request->student_type);
        }
        if ($this->request->filled('entry_mode')) {
            $query->whereJsonContains('entry_mode', $this->request->entry_mode);
        }
        if ($this->request->filled('level')) {
            $query->whereJsonContains('level', $this->request->level);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Payment Type',
            'Amount (₦)',
            'Session',
            'Semesters',
            'Level(s)',
            'Student Type',
            'Entry Mode',
            'Sexes',
            'Installment Allowed',
            'No. of Installments',
            'Percentages',
            'Faculties',
            'Departments',
            'Description',
            'Created At',
        ];
    }

    public function map($setting): array
    {
        return [
            ucfirst($setting->payment_type),
            number_format($setting->amount, 2),
            $setting->session,
            implode(', ', (array) ($setting->semesters ?? [])),
            implode(', ', (array) ($setting->level ?? [])),
            implode(', ', (array) ($setting->student_type ?? [])),
            implode(', ', (array) ($setting->entry_mode ?? [])),
            implode(', ', (array) ($setting->sexes ?? [])),
            $setting->installmental_allow_status ? 'Yes' : 'No',
            $setting->number_of_instalment,
            implode('% - ', (array) ($setting->list_instalment_percentage ?? [])),
            implode(', ', $setting->faculties()->pluck('faculty_code')->toArray()),
            implode(', ', $setting->departments()->pluck('department_code')->toArray()),
            $setting->description,
            $setting->created_at->format('d M Y'),
        ];
    }
}
