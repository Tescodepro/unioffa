<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 10px; 
            line-height: 1.3;
            color: #1a5f1a;
            background: #fff;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 10px;
        }
        
        .letter-head { 
            text-align: center; 
            margin-bottom: 8px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            padding: 8px;
            border-radius: 6px;
        }
        
        .letter-head img { 
            width: 100%; 
            max-height: 80px; 
            object-fit: contain;
            filter: brightness(0) invert(1);
        }
        
        .header { 
            text-align: center; 
            margin-bottom: 10px;
            background: #f0fdf4;
            padding: 8px;
            border-radius: 4px;
            border-left: 3px solid #22c55e;
        }
        
        .header h4 { 
            margin: 0; 
            font-size: 16px;
            color: #16a34a;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 10px;
            color: #166534;
        }
        
        .student-info { 
            display: flex; 
            justify-content: space-between; 
            margin: 10px 0;
            background: #f9fafb;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        
        .student-details { 
            width: 75%; 
        }
        
        .student-details p {
            margin-bottom: 4px;
            font-size: 10px;
        }
        
        .student-details strong {
            color: #166534;
        }
        
        .student-passport { 
            width: 20%; 
            text-align: right; 
        }
        
        .student-passport img { 
            width: 80px; 
            height: 80px; 
            border: 2px solid #22c55e; 
            border-radius: 6px;
            object-fit: cover; 
        }
        
        .semester-title { 
            margin-top: 15px; 
            font-size: 12px; 
            font-weight: bold;
            background: linear-gradient(90deg, #16a34a, #22c55e);
            color: white;
            padding: 6px 10px;
            border-radius: 4px 4px 0 0;
            margin-bottom: 0;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 0;
            font-size: 9px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        table, th, td { 
            border: 1px solid #d1d5db; 
        }
        
        th { 
            padding: 5px 6px; 
            text-align: left;
            background: #f0fdf4;
            font-weight: 600;
            color: #166534;
            font-size: 9px;
        }
        
        td { 
            padding: 4px 6px; 
            text-align: left; 
            vertical-align: middle;
        }
        
        tbody tr:nth-child(even) {
            background: #fafafa;
        }
        
        tbody tr:hover {
            background: #f0fdf4;
        }
        
        tbody tr td:first-child {
            font-weight: 600;
            color: #166534;
        }
        
        tfoot tr {
            background: #22c55e;
            color: white;
            font-weight: 600;
        }
        
        tfoot td {
            border-color: #16a34a;
            padding: 6px;
        }
        
        .empty-row {
            text-align: center !important;
            color: #6b7280;
            font-style: italic;
            background: #f9fafb !important;
        }
        
        .summary-section {
            margin: 15px 0 10px;
        }
        
        .summary-table {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #22c55e;
            border-radius: 8px;
            margin-top: 0;
        }
        
        .summary-table td {
            padding: 8px;
            font-weight: 600;
            color: #16a34a;
            font-size: 11px;
        }
        
        .summary-table .total-value {
            font-size: 14px;
            font-weight: 700;
        }
        
        .signature-section { 
            margin: 20px 0 10px;
        }
        
        .signature-table {
            border: none;
            margin-top: 0;
        }
        
        .signature-table td {
            border: none;
            text-align: center;
            padding: 15px 5px 5px;
            width: 33.33%;
            position: relative;
        }
        
        .signature-table td::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 10%;
            right: 10%;
            height: 1px;
            background: #16a34a;
        }
        
        .signature-label {
            font-size: 9px;
            color: #166534;
            font-weight: 500;
        }
        
        .letter-footer { 
            margin: 15px 0 8px;
            text-align: center;
            background: #f0fdf4;
            padding: 6px;
            border-radius: 4px;
        }
        
        .letter-footer img { 
            width: 100%; 
            max-height: 60px; 
            object-fit: contain; 
        }
        
        .footer { 
            margin-top: 10px; 
            font-size: 8px; 
            text-align: center;
            color: #6b7280;
            background: #f9fafb;
            padding: 6px;
            border-radius: 4px;
            border-top: 2px solid #22c55e;
        }
        
        @media print {
            .container { 
                padding: 5px;
                max-width: none;
            }
            body { font-size: 9px; }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- LETTER HEAD --}}
        <div class="letter-head">
            <img src="{{ public_path('portal_assets/img/users/letter_head.png') }}" alt="Letter Head">
        </div>

        <div class="header">
            <h4>Course Registration Form</h4>
            <p><strong>Session:</strong> {{ $session->name ?? 'N/A' }} |
               <strong>Semester:</strong> {{ $semester->name ?? 'N/A' }}</p>
        </div>

        {{-- STUDENT INFO --}}
        <div class="student-info">
            <div class="student-details">
                <p><strong>Student:</strong> {{ $student->first_name }} {{ $student->last_name }} ({{ $student->username }})</p>
                <p><strong>Email:</strong> {{ $student->email }} | <strong>Phone:</strong> {{ $student->phone }}</p>
            </div>
            <div class="student-passport">
                @php
                    $passportPath = $student->profile_picture && file_exists(public_path($student->profile_picture))
                        ? public_path($student->profile_picture)
                        : public_path('portal_assets/img/users/placeholder.jpeg'); // fallback image
                @endphp
                <img src="{{ $passportPath }}" alt="Passport">
            </div>
        </div>

        {{-- FIRST SEMESTER --}}
        <div class="semester-title">First Semester Courses</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%">Course Code</th>
                    <th style="width: 50%">Course Title</th>
                    <th style="width: 15%">Unit</th>
                    <th style="width: 15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $firstSemesterUnits = 0; @endphp
                @forelse($registeredCourses->where('course.semester', '1st') as $reg)
                    <tr>
                        <td>{{ $reg->course->course_code }}</td>
                        <td>{{ $reg->course->course_title }}</td>
                        <td>{{ $reg->course->course_unit }}</td>
                        <td>{{ $reg->course->course_status ?? 'N/A' }}</td>
                    </tr>
                    @php $firstSemesterUnits += $reg->course->course_unit; @endphp
                @empty
                    <tr>
                        <td colspan="4" class="empty-row">No First Semester courses registered.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total Units</strong></td>
                    <td colspan="2"><strong>{{ $firstSemesterUnits }}</strong></td>
                </tr>
            </tfoot>
        </table>

        {{-- SECOND SEMESTER --}}
        <div class="semester-title">Second Semester Courses</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%">Course Code</th>
                    <th style="width: 50%">Course Title</th>
                    <th style="width: 15%">Unit</th>
                    <th style="width: 15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $secondSemesterUnits = 0; @endphp
                @forelse($registeredCourses->where('course.semester', '2nd') as $reg)
                    <tr>
                        <td>{{ $reg->course->course_code }}</td>
                        <td>{{ $reg->course->course_title }}</td>
                        <td>{{ $reg->course->course_unit }}</td>
                        <td>{{ $reg->course->course_status ?? 'N/A' }}</td>
                    </tr>
                    @php $secondSemesterUnits += $reg->course->course_unit; @endphp
                @empty
                    <tr>
                        <td colspan="4" class="empty-row">No Second Semester courses registered.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total Units</strong></td>
                    <td colspan="2"><strong>{{ $secondSemesterUnits }}</strong></td>
                </tr>
            </tfoot>
        </table>

        {{-- OVERALL SUMMARY IN TABLE FORMAT --}}
        <div class="summary-section">
            <div class="semester-title">Overall Summary</div>
            <table class="summary-table">
                <tr>
                    <td style="width: 70%"><strong>Total Units (All Semesters):</strong></td>
                    <td class="total-value">{{ $firstSemesterUnits + $secondSemesterUnits }}</td>
                </tr>
            </table>
        </div>

        {{-- SIGNATURE SECTION IN TABLE FORMAT --}}
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-label">Student's Signature</div>
                    </td>
                    <td>
                        <div class="signature-label">Head of Department</div>
                    </td>
                    <td>
                        <div class="signature-label">Dean's Signature</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- LETTER FOOTER --}}
        <div class="letter-footer">
            <img src="{{ public_path('portal_assets/img/users/letter_head_footer.png') }}" alt="Letter Head Footer">
        </div>

        <div class="footer">
            <p>Generated on {{ now()->format('d M, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>