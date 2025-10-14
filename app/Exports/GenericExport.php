<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Transaction;
use App\Models\PaymentSetting;

class GenericExport implements FromCollection
{
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function collection()
    {
        switch ($this->type) {
            case 'faculty':
                return Transaction::with('faculty')->get();
            case 'department':
                return Transaction::with('department')->get();
            case 'level':
                return Transaction::select('level', 'amount', 'status')->get();
            case 'student':
                return Transaction::with('student')->get();
            default:
                return collect([]);
        }
    }
}

