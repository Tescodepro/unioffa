<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Transcript - {{ $student->matric_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px 40px;
        }

        .letter-head {
            width: 100%;
            margin-bottom: 20px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 100, 0, 0.08);
            z-index: -1000;
            pointer-events: none;
            white-space: nowrap;
            font-weight: bold;
        }

        .header-title {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1b5e20;
        }

        .header-title h2 {
            font-size: 16px;
            color: #1b5e20;
            text-transform: uppercase;
            margin: 0;
        }

        .student-info-section {
            margin-bottom: 20px;
            padding: 12px;
            background-color: #f8fdf8;
            border: 1px solid #1b5e20;
        }

        .info-table {
            width: 100%;
        }

        .info-table td {
            padding: 3px;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            width: 110px;
            color: #1b5e20;
        }

        .cgpa-container {
            float: right;
            width: 160px;
            background: #1b5e20;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        .cgpa-container .value {
            font-size: 22px;
            font-weight: bold;
        }

        .clear {
            clear: both;
        }

        .session-container {
            margin-bottom: 20px;
            border: 1px solid #eee;
            page-break-after: always;
        }
        
        .session-container:last-of-type {
            page-break-after: auto;
        }

        .session-header {
            background: #1b5e20;
            color: #fff;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: bold;
            display: block;
        }

        .session-summary {
            background: #f1f8e9;
            color: #1b5e20;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: bold;
            text-align: right;
            border-top: 1px solid #1b5e20;
        }

        .semester-container {
            padding: 10px;
        }

        .semester-header {
            color: #1b5e20;
            margin-bottom: 8px;
            font-size: 11px;
            font-weight: bold;
            border-bottom: 1px solid #c8e6c9;
            padding-bottom: 3px;
        }

        .semester-gpa {
            text-align: right;
            font-weight: bold;
            margin-top: 5px;
            color: #1b5e20;
            font-size: 10px;
        }

        table.results {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        table.results th,
        table.results td {
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 10px;
        }

        table.results th {
            background: #f5f5f5;
            color: #1b5e20;
            text-transform: uppercase;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #1b5e20;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #555;
        }

        .text-success { color: #2e7d32; font-weight: bold; }
        .text-danger { color: #d32f2f; font-weight: bold; }
        .text-warning { color: #f57c00; font-weight: bold; }
    </style>
</head>

<body>
    <div class="watermark">STUDENT COPY</div>

    <!-- Letterhead -->
    @php
        $letterheadPath = \App\Models\SystemSetting::get('letterhead_path', 'portal_assets/img/users/letter_head.png');
        $fullPath = public_path($letterheadPath);
        $base64 = '';
        if (file_exists($fullPath)) {
            $data = base64_encode(file_get_contents($fullPath));
            $base64 = 'data:image/png;base64,' . $data;
        }
    @endphp

    @if($base64)
        <img src="{{ $base64 }}" class="letter-head">
    @else
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #1b5e20;">{{ strtoupper($schoolName) }}</h1>
            <p>OFFICE OF THE REGISTRAR</p>
        </div>
    @endif

    <div class="header-title">
        <h2>OFFICIAL ACADEMIC TRANSCRIPT</h2>
    </div>

    <!-- Student Information & CGPA -->
    <div class="student-info-section">
        <div class="cgpa-container">
            <div style="font-size: 9px; text-transform: uppercase;">Cumulative GPA</div>
            <div class="value">{{ number_format((float)$cgpa, 2) }}</div>
            <div style="font-size: 8px;">Scale: 5.0</div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">NAME:</td>
                <td>{{ strtoupper($student->user->full_name) }}</td>
            </tr>
            <tr>
                <td class="label">MATRIC NO:</td>
                <td>{{ $student->matric_no }}</td>
            </tr>
            <tr>
                <td class="label">FACULTY:</td>
                <td>{{ strtoupper($student->department->faculty->faculty_name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">DEPARTMENT:</td>
                <td>{{ strtoupper($student->department->department_name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">PROGRAMME:</td>
                <td>{{ strtoupper($student->programme) }}</td>
            </tr>
            <tr>
                <td class="label">LEVEL:</td>
                <td>{{ $student->level }}</td>
            </tr>
        </table>
        <div class="clear"></div>
    </div>

    <!-- Results by Session -->
    @foreach($resultsBySession as $session => $sessionData)
        <div class="session-container">
            <div class="session-header">{{ $session }} ACADEMIC SESSION</div>

            <div class="semester-container">
                @foreach($sessionData['semesters'] as $semCode => $semesterData)
                    <div class="semester-header">{{ $semCode == '1st' ? 'FIRST' : 'SECOND' }} SEMESTER</div>
                    
                    <table class="results">
                        <thead>
                            <tr>
                                <th style="width: 12%;">CODE</th>
                                <th style="width: 42%;">COURSE TITLE</th>
                                <th style="width: 8%;">UNIT</th>
                                <th style="width: 8%;">CA</th>
                                <th style="width: 8%;">EXAM</th>
                                <th style="width: 8%;">TOTAL</th>
                                <th style="width: 8%;">GRADE</th>
                                <th style="width: 10%;">REMARK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($semesterData['results'] as $result)
                                <tr>
                                    <td class="center"><strong>{{ $result->course_code }}</strong></td>
                                    <td>{{ strtoupper($result->course_title) }}</td>
                                    <td class="center">{{ $result->course_unit }}</td>
                                    <td class="center">{{ $result->ca }}</td>
                                    <td class="center">{{ $result->exam }}</td>
                                    <td class="center"><strong>{{ $result->total }}</strong></td>
                                    <td class="center"><strong>{{ $result->grade }}</strong></td>
                                    <td class="center 
                                        @if(in_array($result->remark, ['Excellent', 'Very Good', 'Good', 'Pass'])) text-success 
                                        @elseif($result->remark == 'Fair') text-warning 
                                        @else text-danger 
                                        @endif">
                                        {{ strtoupper($result->remark) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="semester-gpa">SEMESTER GPA: {{ $semesterData['gpa'] }}</div>
                @endforeach
            </div>

            <div class="session-summary">SESSION GPA: {{ $sessionData['gpa'] }}</div>
        </div>
    @endforeach

    <!-- Footer -->
    <div class="footer">
        <p><strong>THIS IS AN OFFICIAL DOCUMENT GENERATED BY THE PORTAL SERVICE OF {{ strtoupper($schoolName) }}</strong></p>
        <p>FOR ANY VERIFICATION, PLEASE CONTACT THE OFFICE OF THE REGISTRAR</p>
        <p>DATE GENERATED: {{ now()->format('F d, Y @ H:i:s') }} | STUDENT COPY ONLY</p>
    </div>
</body>

</html>