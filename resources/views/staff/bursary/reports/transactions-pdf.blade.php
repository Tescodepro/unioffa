<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>University of Offa — Transactions Report</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #222;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        .header h1 {
            margin: 5px 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: normal;
        }
        .header p {
            margin: 3px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .text-success { color: green; }
        .text-danger { color: red; }
        .text-warning { color: #e0a800; }

        .footer {
            text-align: right;
            font-size: 11px;
            margin-top: 20px;
            border-top: 1px solid #aaa;
            padding-top: 5px;
        }

        .report-title {
            text-align: center;
            font-size: 16px;
            margin-top: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('assets/images/logo.png') }}" alt="University Logo">
        <h1>University of Offa</h1>
        <p><strong>Motto:</strong> Knowledge, Integrity, and Service</p>
        <p>PMB 1020, Offa, Kwara State, Nigeria</p>
        <p>Email: info@unioffa.edu.ng | Website: www.unioffa.edu.ng</p>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        Transactions Report
    </div>
    <p><strong>Generated On:</strong> {{ now()->format('d M, Y h:i A') }}</p>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Reference</th>
                <th>Student</th>
                <th>Payment Type</th>
                <th>Amount (₦)</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $i => $txn)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $txn->refernce_number }}</td>
                    <td>{{ optional($txn->user)->first_name }} {{ optional($txn->user)->last_name }}</td>
                    <td>{{ ucfirst($txn->payment_type) }}</td>
                    <td>{{ number_format($txn->amount, 2) }}</td>
                    <td>
                        @if($txn->payment_status == 1)
                            <span class="text-success">Successful</span>
                        @elseif($txn->payment_status == 2)
                            <span class="text-danger">Failed</span>
                        @else
                            <span class="text-warning">Pending</span>
                        @endif
                    </td>
                    <td>{{ $txn->created_at->format('d M, Y h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">No transaction records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Generated automatically by the Bursary System — {{ config('app.name') }}
    </div>

</body>
</html>
