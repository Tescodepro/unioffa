<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
        }

        .letterhead {
            width: 100%;
            display: block;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            display: block;
        }

        .watermark {
            position: fixed;
            top: 30%;
            left: 15%;
            width: 70%;
            opacity: 0.06;
            z-index: -1;
        }

        .content {
            padding: 20px 40px 40px 40px;
            position: relative;
            z-index: 1;
        }

        h3 {
            text-align: center;
            margin: 20px 0;
            text-decoration: underline;
            font-size: 15pt;
        }

        .student-info, .transaction-info {
            margin: 20px 0;
            line-height: 1.6;
        }

        .info-block {
            margin-bottom: 4px;
        }

        strong {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 4px 10px;
            text-align: left;
            font-size: 12pt;
        }

        table th {
            background: #f2f2f2;
            text-transform: uppercase;
        }

        .signature-block {
            margin-top: 40px;
            text-align: left;
        }

        .signature-name {
            margin-top: 10px;
            font-weight: bold;
            font-size: 13pt;
        }

        .signature-title {
            font-style: italic;
            font-size: 12pt;
            margin-top: -5px;
        }

        .qrcode {
            float: right;
            margin-top: -50px;
        }

        /* Print styles */
        @media print {
            body { font-size: 11pt; }
            .content { padding: 10px 50px 100px 50px; }
        }

        @page {
            margin: 0;
            size: A4;
        }
    </style>
</head>
<body>

    <!-- Letterhead -->
    <img src="{{ public_path('portal_assets/img/users/letter_head.png') }}" class="letterhead">

    <!-- Watermark -->
    <img src="{{ public_path('portal_assets/img/users/letter_head.png') }}" class="watermark">

    <div class="content">

        <h3>OFFICIAL PAYMENT RECEIPT</h3>

        <div class="student-info">
            <div class="info-block"><strong>Student Name:</strong> {{ strtoupper($user->full_name) }}</div>
            @if ($user->student)
                <div class="info-block"><strong>Matric Number:</strong> {{ $user->student->matric_no}}</div>
                <div class="info-block"><strong>Programme:</strong> {{ $user->student->programme }}</div>
                <div class="info-block"><strong>Department:</strong> {{ $user->student->department->department_name  }}</div>                
                <div class="info-block"><strong>Level:</strong> {{ $user->student->level }}</div>
            @else
                <div class="info-block"><strong>Registration No.:</strong> {{ $user->registration_no }}</div>
            @endif
            
        </div>

        <table>
            <thead>
                <tr>
                    <th>Transaction Ref</th>
                    <th>Payment Type</th>
                    <th>Session</th>
                    <th>Amount =N= </th>
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

        <div class="transaction-info">
            <strong>Status:</strong> PAID <br>
            <strong>Date Issued:</strong> {{ $date }}
        </div>

        <!-- QR Code -->
        <div class="qrcode">
            @php
                $qrData = route('verify.receipt', ['ref' => $transaction->refernce_number]);
                $qrCode = base64_encode(QrCode::format('png')->size(100)->generate($qrData));
            @endphp
            <img src="data:image/png;base64, {!! $qrCode !!}" alt="QR Code">
            <div style="font-size: 10pt; text-align:center;">Scan to verify</div>
        </div>

        <!-- Signature -->
        <div class="signature-block">
            <p class="signature-name">Mr. Salaudeen OYEWALE</p>
            <p class="signature-title">Ag. Bursar</p>
        </div>

    </div>

    <!-- Footer -->
    <img src="{{ public_path('portal_assets/img/users/letter_head_footer.png') }}" class="footer">

</body>
</html>
 