<?php
$count = \App\Models\Transaction::where('payment_status', 1)
    ->where('session', '2025/2026')
    ->where(function ($q) {
        $q->where('payment_type', '!=', 'technical')
          ->orWhere('id', function($sub) {
              $sub->select('id')
                  ->from('transactions as t2')
                  ->whereColumn('t2.user_id', 'transactions.user_id')
                  ->where('t2.payment_status', 1)
                  ->where('t2.payment_type', 'technical')
                  ->whereColumn('t2.session', 'transactions.session')
                  ->orderBy('created_at', 'asc')
                  ->limit(1);
          });
    })->count();

echo 'Count with subquery limit 1: ' . $count . PHP_EOL;
