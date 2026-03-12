<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Transcript - {{ $student->username }}</title>
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
            margin: 5mm 0 5mm 0;
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
        .session-title {
            font-weight: bold;
            font-size: 12pt;
            background-color: #f2f2f2;
            padding: 3px 10px;
            margin-top: 10mm;
            border: 1px solid #000;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .results-table th, .results-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            font-size: 9pt;
            word-wrap: break-word;
        }
        .results-table th {
            background-color: #f9f9f9;
            font-weight: bold;
            text-transform: uppercase;
        }
        .metrics-row {
            background-color: #fdfdfd;
            font-weight: bold;
            font-size: 10pt;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .final-summary {
            margin-top: 15mm;
            border: 2px solid #000;
            padding: 5mm;
        }
        .final-summary h3 {
            margin-top: 0;
            text-align: center;
            text-decoration: underline;
        }
        .summary-grid {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .summary-item b {
            font-size: 14pt;
            display: block;
        }

        .signature-section {
            margin-top: 20mm;
            text-align: right;
            page-break-inside: avoid;
        }
        .signature-box {
            display: inline-block;
            width: 300px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 15mm;
            padding-top: 2mm;
        }
        .footer-wrapper {
            margin-top: auto;
        }
        .footer-image {
            width: 100%;
            display: block;
            margin-top: 5mm;
        }
        .page-break {
            page-break-before: always;
        }
        .session-wrapper {
            margin-bottom: 10mm;
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
            <h2 class="report-title">Student Academic Transcript</h2>
        </div>

        <table class="metadata-table">
            <tr>
                <td style="width: 60%;">
                    <strong>Full Name:</strong> {{ $student->fullname }}<br>
                    <strong>Matric Number:</strong> {{ $student->username }}<br>
                    <strong>Faculty:</strong> {{ $student->student->department->faculty->faculty_name ?? '---' }}<br>
                    <strong>Department:</strong> {{ $student->student->department->department_name ?? '---' }}
                </td>
                <td class="text-end">
                    <strong>Level:</strong> {{ $student->student->level ?? '---' }} Level<br>
                    <strong>Date Generated:</strong> {{ date('d-M-Y') }}<br>
                    <strong>Status:</strong> Official
                </td>
            </tr>
        </table>

        @php $sessionCount = 0; @endphp
        @foreach($resultsBySession as $session => $results)
            <div class="session-wrapper {{ $sessionCount > 0 ? 'page-break' : '' }}">
                @if($sessionCount > 0)
                    <div class="header-content">
                        <h2 class="report-title" style="font-size: 14pt; margin-top: 5mm;">Student Academic Transcript (Cont'd)</h2>
                    </div>
                    <p style="border-bottom: 1px solid #000; padding-bottom: 2mm;"><strong>Name:</strong> {{ $student->fullname }} | <strong>Matric:</strong> {{ $student->username }}</p>
                @endif

                <div class="session-title">SESSION: {{ $session }}</div>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Course Code</th>
                            <th style="width: 45%;">Course Title</th>
                            <th style="width: 10%;" class="text-center">Unit</th>
                            <th style="width: 10%;" class="text-center">CA</th>
                            <th style="width: 10%;" class="text-center">Exam</th>
                            <th style="width: 10%;" class="text-center">Total</th>
                            <th style="width: 10%;" class="text-center">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $r)
                            <tr>
                                <td>{{ $r->course_code }}</td>
                                <td>{{ $r->course_title }}</td>
                                <td class="text-center">{{ $r->course_unit }}</td>
                                <td class="text-center">{{ $r->ca }}</td>
                                <td class="text-center">{{ $r->exam }}</td>
                                <td class="text-center fw-bold">{{ $r->total }}</td>
                                <td class="text-center fw-bold">{{ $r->grade }}</td>
                            </tr>
                        @endforeach
                        <tr class="metrics-row">
                            <td colspan="2" class="text-end">SESSION TOTALS:</td>
                            <td class="text-center">TCO: {{ $sessionMetrics[$session]['tco'] }}</td>
                            <td colspan="2" class="text-center">TCP: {{ $sessionMetrics[$session]['tcp'] }}</td>
                            <td class="text-center">TWGP: {{ $sessionMetrics[$session]['twgp'] }}</td>
                            <td class="text-center">GPA: {{ $sessionMetrics[$session]['gpa'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @php $sessionCount++; @endphp
        @endforeach

        <div class="final-summary">
            <h3>FINAL CUMULATIVE PERFORMANCE</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <small>CTCO</small>
                    <b>{{ $finalMetrics['ctco'] }}</b>
                </div>
                <div class="summary-item">
                    <small>CTCP</small>
                    <b>{{ $finalMetrics['ctcp'] }}</b>
                </div>
                <div class="summary-item">
                    <small>CTWGP</small>
                    <b>{{ $finalMetrics['ctwgp'] }}</b>
                </div>
                <div class="summary-item">
                    <small>CGPA</small>
                    <b style="font-size: 18pt; color: #000;">{{ $finalMetrics['cgpa'] }}</b>
                </div>
            </div>
        </div>

        <div class="footer-wrapper">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">
                        <strong>University Registrar</strong><br>
                        <small>(Sign, Stamp & Date)</small>
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
