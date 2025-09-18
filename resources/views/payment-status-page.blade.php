<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
        }
        .card {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #198754;
            margin-bottom: 15px;
        }
        p {
            color: #6c757d;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #198754; /* Bootstrap green */
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #157347;
        }
        .status {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .status.success {
            background: #d1e7dd;
            color: #0f5132;
        }
        .status.failed {
            background: #f8d7da;
            color: #842029;
        }
    </style>
</head>
<body>

    <div class="card">
        @include('layouts.flash-message')

        <h2>Payment Status</h2>

        @if($transaction && $transaction->payment_status == 1)
            <div class="status success">✅ Payment Successful</div>
        @else
            <div class="status failed">❌ Payment Failed or Pending</div>
        @endif

        <p>
            Payment Type: <strong>{{ ucfirst($paymentType) }}</strong>
        </p>

        <a href="{{ $backRoute }}" class="btn">Back to Dashboard</a>
    </div>

</body>
</html>
