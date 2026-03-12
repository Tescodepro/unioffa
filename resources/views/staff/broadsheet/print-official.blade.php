<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broadsheet Report - {{ $department->department_name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
            line-height: 1.2;
            font-size: 8pt;
            height: 100%;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .letter-head {
            width: 100%;
            height: auto;
            display: block;
            margin-bottom: 2mm;
        }
        .university-name {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
        }
        .faculty-name {
            font-size: 30pt;
            font-weight: bold;
            margin: 0;
            color: #047d1fff;
        }
        .department-name {
            font-size: 25pt;
            font-weight: bold;
            margin: 0;
        }
        .report-subtitle {
            font-size: 20pt;
            margin: 1mm 0;
        }

        .broadsheet-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .broadsheet-table th, .broadsheet-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            word-wrap: break-word;
            font-size: 7.5pt;
        }
        .broadsheet-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-left { text-align: left !important; }

        /* Multi-row header styling */
        .header-row-courses th {
            font-size: 6.5pt;
            vertical-align: bottom;
            height: 60px;
        }
        .header-row-courses span {
            display: block;
            /* transform: rotate(-90deg); */ /* Uncomment if names are long and need rotation */
        }

        .section-header {
            background-color: #e9ecef !important;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-good { color: green; font-weight: bold; }
        .status-probation { color: red; font-weight: bold; }
        .outstanding-list { color: #d63384; font-size: 6.5pt; line-height: 1; }

        .footer-wrapper {
            margin-top: auto;
            padding-top: 5mm;
        }
        .summary-sections {
            display: flex;
            justify-content: space-between;
            gap: 5mm;
        }
        .summary-box {
            flex: 1;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table th, .summary-table td {
            border: 1px solid #000;
            padding: 2px 4px;
            font-size: 7.5pt;
        }
        .summary-table th { background: #f2f2f2; text-align: left; }

        .signature-row {
            margin-top: 10mm;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .sig-box {
            width: 30%;
            border-top: 1px solid #000;
            padding-top: 2mm;
        }

        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <img src="{{ asset(\App\Models\SystemSetting::get('letterhead_path', 'portal_assets/img/users/letter_head.png')) }}" class="letter-head">
            <h2 class="faculty-name">{{ strtoupper($department->faculty->faculty_name ?? '') }}</h2>
            <h3 class="department-name">Department of {{ $department->department_name }}</h3>
            <p class="report-subtitle">
                <strong>{{ $session->name }} {{ $semester ? $semester->name : 'Sessional' }} Broadsheet</strong> &mdash; 
                {{ $level }} Level
            </p>
        </div>

        <table class="broadsheet-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25px;">SN</th>
                    <th rowspan="2" style="width: 100px;">MATRIC NO</th>
                    @foreach($course_codes as $code)
                        <th class="header-row-courses">
                            <span>{{ $code }}</span>
                            <br>
                            <small>{{ $courses_info[$code]->course_unit ?? '' }}C</small>
                        </th>
                    @endforeach
                    <th colspan="3" class="section-header">CURRENT</th>
                    <th colspan="3" class="section-header">PREVIOUS</th>
                    <th colspan="3" class="section-header">CUMULATIVE</th>
                    <th rowspan="2" style="width: 120px;">OUTSTANDING COURSES</th>
                    <th rowspan="2" style="width: 80px;">ACADEMIC STATUS</th>
                </tr>
                <tr>
                    @foreach($course_codes as $code)
                        <th></th>
                    @endforeach
                    <!-- Current -->
                    <th style="width: 25px;">TU</th><th style="width: 25px;">TGP</th><th style="width: 35px;">GPA</th>
                    <!-- Previous -->
                    <th style="width: 25px;">TU</th><th style="width: 25px;">TGP</th><th style="width: 35px;">GPA</th>
                    <!-- Cumulative -->
                    <th style="width: 25px;">TU</th><th style="width: 25px;">TGP</th><th style="width: 35px;">CGPA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students_data as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left fw-bold">{{ $data['student']->matric_no }}</td>
                        
                        @foreach($course_codes as $code)
                            <td>{{ $data['course_results'][$code] }}</td>
                        @endforeach

                        <!-- Current Metrics -->
                        <td>{{ $data['current']['tco'] }}</td>
                        <td>{{ $data['current']['wgp'] }}</td>
                        <td class="fw-bold">{{ number_format($data['current']['gpa'], 2) }}</td>

                        <!-- Previous Metrics -->
                        <td>{{ $data['previous']['tco'] }}</td>
                        <td>{{ $data['previous']['wgp'] }}</td>
                        <td>{{ number_format($data['previous']['gpa'], 2) }}</td>

                        <!-- Cumulative Metrics -->
                        <td>{{ $data['cumulative']['tco'] }}</td>
                        <td>{{ $data['cumulative']['wgp'] }}</td>
                        <td class="fw-bold">{{ number_format($data['cumulative']['gpa'], 2) }}</td>

                        <!-- Outstanding -->
                        <td class="text-left outstanding-list">
                            @foreach($data['outstanding'] as $course)
                                [{{ $course }}]{{ !$loop->last ? '||' : '' }}
                            @endforeach
                        </td>

                        <!-- Status -->
                        <td class="{{ $data['academic_status'] === 'GOOD STANDING' ? 'status-good' : 'status-probation' }}">
                            {{ $data['academic_status'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer-wrapper">
            <div class="summary-sections">
                <!-- Course Key -->
                <div class="summary-box">
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">COURSE CODE</th>
                                <th>TITLE</th>
                                <th style="width: 40px;">UNIT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course_codes as $code)
                                <tr>
                                    <td>{{ $code }}</td>
                                    <td class="text-left">{{ $courses_info[$code]->course_title ?? '---' }}</td>
                                    <td>{{ $courses_info[$code]->course_unit ?? '0' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Grading System -->
                <div class="summary-box" style="flex: 0.6;">
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th colspan="3" style="text-align: center;">GRADING SYSTEM</th>
                            </tr>
                            <tr>
                                <th>Mark (%)</th>
                                <th>Letter Grade</th>
                                <th>Grade Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>70-100</td><td>A</td><td>5</td></tr>
                            <tr><td>60-69</td><td>B</td><td>4</td></tr>
                            <tr><td>50-59</td><td>C</td><td>3</td></tr>
                            <tr><td>45-49</td><td>D</td><td>2</td></tr>
                            <tr><td>40-44</td><td>E</td><td>1</td></tr>
                            <tr><td>0-39</td><td>F</td><td>0</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Result Summary Stats -->
                <div class="summary-box" style="flex: 0.8;">
                    <table class="summary-table">
                        <thead>
                            <tr><th colspan="2" style="text-align: center;">RESULT SUMMARY</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Number of Students</td><td style="text-align: right;">{{ $stats['total_students'] }}</td></tr>
                            <tr><td>Students in Good Standing</td><td style="text-align: right;">{{ $stats['total_students'] - $stats['repeats'] }}</td></tr>
                            <tr><td>Students on Probation</td><td style="text-align: right;">{{ $stats['repeats'] }}</td></tr>
                            <tr><td>Students Advised to Withdraw</td><td style="text-align: right;">{{ $stats['cgpa_classes']['withdrawal'] }}</td></tr>
                        </tbody>
                    </table>

                    <div style="margin-top: 5mm;">
                        <table class="summary-table">
                            <thead>
                                <tr><th colspan="2" style="text-align: center;">KEY</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>TU</td><td>Total Units</td></tr>
                                <tr><td>TGP</td><td>Total Grade Points</td></tr>
                                <tr><td>GPA</td><td>Grade Point Average</td></tr>
                                <tr><td>CGPA</td><td>Cumulative GPA</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="signature-row">
                <div class="sig-box">
                    <strong>PREPARED BY (EXAMS OFFICER)</strong><br>
                    <small>Name & Signature</small>
                </div>
                <div class="sig-box">
                    <strong>HEAD OF DEPARTMENT</strong><br>
                    <small>Name & Signature</small>
                </div>
                <div class="sig-box">
                    <strong>DEAN OF FACULTY</strong><br>
                    <small>Name & Signature</small>
                </div>
            </div>
        </div>
    </div>

    <div class="no-print" style="position: fixed; top: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 10px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Print Report</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>
</body>
</html>
