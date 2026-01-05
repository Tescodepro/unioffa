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
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .header p {
            margin: 3px 0;
        }

        .student-info {
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
        }

        .info-table .label {
            font-weight: bold;
            width: 150px;
        }

        .cgpa-box {
            background: #f0f0f0;
            padding: 10px;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #333;
        }

        .cgpa-box h3 {
            margin-bottom: 5px;
        }

        .session-header {
            background: #333;
            color: #fff;
            padding: 8px;
            margin-top: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .semester-header {
            background: #666;
            color: #fff;
            padding: 6px;
            margin-top: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        table.results {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.results th,
        table.results td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }

        table.results th {
            background: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }

        table.results td.center {
            text-align: center;
        }

        .footer {
            margin-top: 40px;
            border-top: 2px solid #000;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>University of Offa</h1>
        <h2>Official Academic Transcript</h2>
        <p>Office of the Registrar</p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <table class="info-table">
            <tr>
                <td class="label">Student Name:</td>
                <td>{{ strtoupper($student->user->full_name) }}</td>
                <td class="label">Matric Number:</td>
                <td>{{ $student->matric_no }}</td>
            </tr>
            <tr>
                <td class="label">Department:</td>
                <td>{{ $student->department->name ?? 'N/A' }}</td>
                <td class="label">Faculty:</td>
                <td>{{ $student->department->faculty->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Programme:</td>
                <td>{{ $student->programme }}</td>
                <td class="label">Entry Mode:</td>
                <td>{{ $student->entry_mode }}</td>
            </tr>
        </table>
    </div>

    <!-- CGPA -->
    <div class="cgpa-box">
        <h3>Cumulative Grade Point Average (CGPA)</h3>
        <h2 style="font-size: 24px; margin: 5px 0;">{{ $cgpa }}</h2>
        <p>On a 5.0 scale</p>
    </div>

    <!-- Results by Session -->
    @foreach($resultsBySession as $session => $results)
        <div class="session-header">{{ $session }} Academic Session</div>

        @php
            $sessionResults = collect($results);
            $firstSemester = $sessionResults->where('semester', 'First');
            $secondSemester = $sessionResults->where('semester', 'Second');
        @endphp

        @if($firstSemester->isNotEmpty())
            <div class="semester-header">First Semester</div>
            <table class="results">
                <thead>
                    <tr>
                        <th style="width: 15%;">Course Code</th>
                        <th style="width: 35%;">Course Title</th>
                        <th style="width: 8%;">Units</th>
                        <th style="width: 8%;">CA</th>
                        <th style="width: 8%;">Exam</th>
                        <th style="width: 8%;">Total</th>
                        <th style="width: 8%;">Grade</th>
                        <th style="width: 10%;">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($firstSemester as $result)
                        <tr>
                            <td class="center"><strong>{{ $result->course_code }}</strong></td>
                            <td>{{ $result->course_title }}</td>
                            <td class="center">{{ $result->course_unit }}</td>
                            <td class="center">{{ $result->ca }}</td>
                            <td class="center">{{ $result->exam }}</td>
                            <td class="center"><strong>{{ $result->total }}</strong></td>
                            <td class="center"><strong>{{ $result->grade }}</strong></td>
                            <td class="center">{{ $result->remark }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($secondSemester->isNotEmpty())
            <div class="semester-header">Second Semester</div>
            <table class="results">
                <thead>
                    <tr>
                        <th style="width: 15%;">Course Code</th>
                        <th style="width: 35%;">Course Title</th>
                        <th style="width: 8%;">Units</th>
                        <th style="width: 8%;">CA</th>
                        <th style="width: 8%;">Exam</th>
                        <th style="width: 8%;">Total</th>
                        <th style="width: 8%;">Grade</th>
                        <th style="width: 10%;">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($secondSemester as $result)
                        <tr>
                            <td class="center"><strong>{{ $result->course_code }}</strong></td>
                            <td>{{ $result->course_title }}</td>
                            <td class="center">{{ $result->course_unit }}</td>
                            <td class="center">{{ $result->ca }}</td>
                            <td class="center">{{ $result->exam }}</td>
                            <td class="center"><strong>{{ $result->total }}</strong></td>
                            <td class="center"><strong>{{ $result->grade }}</strong></td>
                            <td class="center">{{ $result->remark }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <!-- Footer -->
    <div class="footer">
        <p><strong>This is an official document generated by the University of Offa</strong></p>
        <p>Date Generated: {{ now()->format('F d, Y') }}</p>
    </div>
</body>

</html>