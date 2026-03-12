<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departmental Summary Report - {{ $selectedDepartment->department_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
            line-height: 1.4;
            font-size: 11pt;
            height: 100%;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .letterhead {
            width: 100%;
            display: block;
            margin-bottom: 5mm;
        }
        .header-content {
            text-align: center;
        }
        .report-title {
            text-decoration: underline;
            font-weight: bold;
            font-size: 16pt;
            margin: 10mm 0 5mm 0;
            text-transform: uppercase;
        }
        .metadata-table {
            width: 100%;
            border-bottom: 1px solid #000;
            padding-bottom: 3mm;
            margin-bottom: 5mm;
            font-size: 12pt;
        }
        .metadata-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5mm;
            table-layout: fixed;
        }
        .results-table th, .results-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
            font-size: 11pt;
            word-wrap: break-word;
            overflow: hidden;
        }
        .results-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .signature-section {
            margin-top: 15mm;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 12mm;
            padding-top: 2mm;
        }
        .footer-wrapper {
            margin-top: auto;
        }
        .footer-image {
            width: 100%;
            display: block;
            margin-top: 10mm;
        }

        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <img src="{{ asset(\App\Models\SystemSetting::get('letterhead_path', 'portal_assets/img/users/letter_head.png')) }}" class="letterhead">

        <div class="header-content">
            <h2 class="report-title">Departmental Summary Report</h2>
        </div>

        <table class="metadata-table">
            <tr>
                <td style="width: 70%;">
                    <strong>Faculty:</strong> {{ $selectedDepartment->faculty->faculty_name ?? '---' }}<br>
                    <strong>Department:</strong> {{ $selectedDepartment->department_name ?? '---' }}
                </td>
                <td class="text-end">
                    <strong>Level:</strong> {{ request('level') }} Level
                </td>
            </tr>
        </table>

        <table class="results-table">
            <thead>
                <tr>
                    <th style="width: 8%;">S/N</th>
                    <th style="width: 35%;">Student Name</th>
                    <th style="width: 25%;">Matric Number</th>
                    <th style="width: 10%;" class="text-center">Units Offered</th>
                    <th style="width: 10%;" class="text-center">Units Passed</th>
                    <th style="width: 12%;" class="text-center">CGPA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $student->fullname }}</td>
                        <td>{{ $student->username }}</td>
                        <td class="text-center">{{ $student->units_offered }}</td>
                        <td class="text-center">{{ $student->units_passed }}</td>
                        <td class="text-center fw-bold">{{ $student->cgpa }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer-wrapper">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">
                        <strong>Head of Department (HOD)</strong><br>
                        <small>Sign & Date</small>
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        <strong>Registrar / Exams & Records</strong><br>
                        <small>Sign & Date</small>
                    </div>
                </div>
            </div>

            <img src="{{ asset('portal_assets/img/users/letter_head_footer.png') }}" class="footer-image">
        </div>
    </div>

    <div class="no-print" style="position: fixed; top: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 10px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Print Again</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Close Tab</button>
    </div>
</body>
</html>
