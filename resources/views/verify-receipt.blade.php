<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: "Segoe UI", Arial, sans-serif;
        }
        .container {
            margin-top: 60px;
            max-width: 800px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .status-success {
            color: green;
            font-weight: bold;
        }
        .status-failed {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h3 class="text-center mb-4">Receipt Verification</h3>

        @if (!$transaction)
            <div class="alert alert-danger text-center">
                ❌ No valid payment found for reference <strong>{{ $ref }}</strong>.
            </div>
        @else
            <div class="mb-3">
                <strong>Student Name:</strong> {{ strtoupper($transaction->user->full_name) }} <br>
                <strong>Reference Number:</strong> {{ $transaction->refernce_number }} <br>
                <strong>Payment Type:</strong> {{ ucfirst($transaction->payment_type) }} <br>
                <strong>Payment Method:</strong> {{ $transaction->payment_method }} <br>
                <strong>Session:</strong> {{ $transaction->session }} <br>
            </div>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Payment Type</th>
                        <th>Session</th>
                        <th>Amount (₦)</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $transaction->refernce_number }}</td>
                        <td>{{ ucfirst($transaction->payment_type) }}</td>
                        <td>{{ $transaction->session }}</td>
                        <td>{{ number_format($transaction->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('F d, Y h:i A') }}</td>
                    </tr>
                </tbody>
            </table>

            <p class="mt-3">
                <strong>Status:</strong> <span class="status-success">✅ Paid</span>
            </p>
        @endif
    </div>
</div>
</body>
</html>
