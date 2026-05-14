<?php

namespace App\Exports;

use App\Models\LatePaymentSetting;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LatePaymentSettingsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = LatePaymentSetting::with('campus')->orderBy('created_at', 'desc');

        if (! empty($this->filters['payment_type'])) {
            $query->where('payment_type', $this->filters['payment_type']);
        }
        if (! empty($this->filters['session'])) {
            $query->where('session', $this->filters['session']);
        }
        if (! empty($this->filters['semester'])) {
            $query->where('semester', $this->filters['semester']);
        }
        if (! empty($this->filters['campus_id'])) {
            $query->where('campus_id', $this->filters['campus_id']);
        }
        if (! empty($this->filters['student_type'])) {
            $query->whereJsonContains('student_type', $this->filters['student_type']);
        }
        if (! empty($this->filters['level'])) {
            $query->whereJsonContains('level', $this->filters['level']);
        }
        if (! empty($this->filters['entry_mode'])) {
            $query->whereJsonContains('entry_mode', $this->filters['entry_mode']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Payment Type',
            'Campus',
            'Penalty Amount',
            'Closing Date',
            'Session',
            'Semester',
            'Entry Mode',
            'Increment Amount',
            'Increment Date',
            'Student Type',
            'Level(s)',
            'Created At',
        ];
    }

    public function map($setting): array
    {
        return [
            ucfirst($setting->payment_type),
            $setting->campus->name ?? 'N/A',
            number_format($setting->late_fee_amount, 2),
            $setting->closing_date->format('Y-m-d H:i:s'),
            $setting->session ?? 'All',
            $setting->semester ?? 'All',
            is_array($setting->entry_mode) ? implode(', ', $setting->entry_mode) : ($setting->entry_mode ?? 'All'),
            $setting->increment_amount ? number_format($setting->increment_amount, 2) : '0.00',
            $setting->increment_date ? $setting->increment_date->format('Y-m-d H:i:s') : 'N/A',
            is_array($setting->student_type) ? implode(', ', $setting->student_type) : ($setting->student_type ?? 'All'),
            is_array($setting->level) ? implode(', ', $setting->level) : ($setting->level ?? 'All'),
            $setting->created_at->format('Y-m-d'),
        ];
    }
}
