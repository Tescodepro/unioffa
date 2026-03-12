<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Course Form - {{ $student->matric_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            background: #fff;
            padding: 20px 40px;
        }

        .letter-head {
            width: 100%;
            margin-bottom: 20px;
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

        .header-title p {
            font-size: 11px;
            font-weight: bold;
            color: #444;
            margin-top: 5px;
        }

        .student-info-section {
            margin-bottom: 20px;
            padding: 12px;
            background-color: #f8fdf8;
            border: 1px solid #1b5e20;
            position: relative;
        }

        .info-table {
            width: 75%;
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

        .passport-container {
            position: absolute;
            right: 12px;
            top: 12px;
            width: 100px;
            height: 100px;
            border: 2px solid #1b5e20;
            padding: 2px;
        }

        .passport-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .semester-title {
            background: #1b5e20;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 0;
        }

        table.courses-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.courses-table th,
        table.courses-table td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            font-size: 10px;
        }

        table.courses-table th {
            background: #f5f5f5;
            color: #1b5e20;
            text-align: left;
            text-transform: uppercase;
        }

        table.courses-table tfoot td {
            background: #f1f8e9;
            font-weight: bold;
            color: #1b5e20;
        }

        .center {
            text-align: center;
        }

        .overall-summary {
            margin-top: 20px;
            background: #1b5e20;
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            text-align: right;
        }

        .signature-section {
            margin-top: 50px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 33.33%;
            padding: 10px;
            text-align: center;
            vertical-align: bottom;
        }

        .sig-line {
            border-top: 1px solid #1b5e20;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 9px;
            color: #1b5e20;
            font-weight: bold;
        }

        .letter-footer {
            width: 100%;
            margin-top: 40px;
        }

        .system-footer {
            margin-top: 20px;
            font-size: 8px;
            text-align: center;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    {{-- LETTER HEAD --}}
    @php
        $schoolName = \App\Models\SystemSetting::get('school_name', 'University of Offa');
        $letterheadPath = \App\Models\SystemSetting::get('letterhead_path', 'portal_assets/img/users/letter_head.png');
        $fullPath = public_path($letterheadPath);
        $base64 = '';
        if (file_exists($fullPath) && is_file($fullPath)) {
            $data = base64_encode(file_get_contents($fullPath));
            $base64 = 'data:image/png;base64,' . $data;
        }
    @endphp

    @if($base64)
        <img src="{{ $base64 }}" class="letter-head">
    @else
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #1b5e20;">UNIVERSITY OF OFFA</h1>
            <p>Portal Course Form</p>
        </div>
    @endif

    <div class="header-title">
        <h2>STUDENT COURSE REGISTRATION FORM</h2>
        <p>{{ $session->name ?? 'N/A' }} ACADEMIC SESSION</p>
    </div>

    {{-- STUDENT INFO --}}
    <div class="student-info-section">
        <div class="passport-container">
            @php
                $passportPath = $student->profile_picture && file_exists(public_path($student->profile_picture))
                    ? public_path($student->profile_picture)
                    : public_path('portal_assets/img/users/placeholder.jpeg');
                
                $passBase64 = '';
                if (file_exists($passportPath) && is_file($passportPath)) {
                    $passData = base64_encode(file_get_contents($passportPath));
                    $passBase64 = 'data:image/jpeg;base64,' . $passData;
                }
            @endphp
            @if($passBase64)
                <img src="{{ $passBase64 }}" alt="Passport">
            @else
                <div style="width: 100%; height: 100%; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #999; text-align: center; padding-top: 40px;">PASSPORT</div>
            @endif
        </div>

        <table class="info-table">
            <tr>
                <td class="label">FULL NAME:</td>
                <td>{{ strtoupper($user->full_name) }}</td>
            </tr>
            <tr>
                <td class="label">MATRIC NO:</td>
                <td>{{ $student->matric_no }}</td>
            </tr>
            <tr>
                <td class="label">PROGRAMME:</td>
                <td>{{ strtoupper($student->programme) }}</td>
            </tr>
            <tr>
                <td class="label">DEPARTMENT:</td>
                <td>{{ strtoupper($student->department?->department_name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">FACULTY:</td>
                <td>{{ strtoupper($student->department?->faculty?->faculty_name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">LEVEL:</td>
                <td>{{ $student->level }}</td>
            </tr>
            <tr>
                <td class="label">EMAIL:</td>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <td class="label">PHONE:</td>
                <td>{{ $user->phone }}</td>
            </tr>
        </table>
    </div>

    @php
        $firstSemResults = $registeredCourses->where('course.semester', '1st');
        $secondSemResults = $registeredCourses->where('course.semester', '2nd');
        $firstSemesterUnits = 0;
        $secondSemesterUnits = 0;
    @endphp

    {{-- FIRST SEMESTER --}}
    <div class="semester-title">FIRST SEMESTER COURSES</div>
    <table class="courses-table">
        <thead>
            <tr>
                <th style="width: 15%">COURSE CODE</th>
                <th style="width: 55%">COURSE TITLE</th>
                <th style="width: 15%; text-align: center;">UNIT</th>
                <th style="width: 15%; text-align: center;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($firstSemResults as $reg)
                <tr>
                    <td><strong>{{ $reg->course->course_code }}</strong></td>
                    <td>{{ strtoupper($reg->course->course_title) }}</td>
                    <td class="center">{{ $reg->course->course_unit }}</td>
                    <td class="center">{{ strtoupper($reg->course->course_status ?? 'N/A') }}</td>
                </tr>
                @php $firstSemesterUnits += $reg->course->course_unit; @endphp
            @empty
                <tr>
                    <td colspan="4" class="center">No courses registered for this semester.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right;">TOTAL FIRST SEMESTER UNITS:</td>
                <td class="center">{{ $firstSemesterUnits }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- SECOND SEMESTER --}}
    @if($secondSemResults->isNotEmpty())
    <div class="semester-title">SECOND SEMESTER COURSES</div>
    <table class="courses-table">
        <thead>
            <tr>
                <th style="width: 15%">COURSE CODE</th>
                <th style="width: 55%">COURSE TITLE</th>
                <th style="width: 15%; text-align: center;">UNIT</th>
                <th style="width: 15%; text-align: center;">STATUS</th>
            </tr>
        </thead>
        <tbody>
                @foreach($secondSemResults as $reg)
                    <tr>
                        <td><strong>{{ $reg->course->course_code }}</strong></td>
                        <td>{{ strtoupper($reg->course->course_title) }}</td>
                        <td class="center">{{ $reg->course->course_unit }}</td>
                        <td class="center">{{ strtoupper($reg->course->course_status ?? 'N/A') }}</td>
                    </tr>
                    @php $secondSemesterUnits += $reg->course->course_unit; @endphp
                @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right;">TOTAL SECOND SEMESTER UNITS:</td>
                <td class="center">{{ $secondSemesterUnits }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="overall-summary">
        OVERALL REGISTERED UNITS: {{ $firstSemesterUnits + $secondSemesterUnits }}
    </div>

    {{-- SIGNATURE SECTION --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="sig-line">STUDENT'S SIGNATURE</div>
                </td>
                <td>
                    <div class="sig-line">HEAD OF DEPARTMENT</div>
                </td>
                <td>
                    <div class="sig-line">ACADEMIC ADVISER</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- LETTER FOOTER --}}
    @php
        $footerPath = public_path('portal_assets/img/users/letter_head_footer.png');
        $footerBase64 = '';
        if (file_exists($footerPath)) {
            $fdata = base64_encode(file_get_contents($footerPath));
            $footerBase64 = 'data:image/png;base64,' . $fdata;
        }
    @endphp

    @if($footerBase64)
        <img src="{{ $footerBase64 }}" class="letter-footer">
    @endif

    <div class="system-footer">
        Generated by UniOffa Portal on {{ now()->format('d M, Y h:i A') }}
    </div>
</body>

</html>
