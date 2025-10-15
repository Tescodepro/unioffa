<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Application Details</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
            font-size: 12pt;
            line-height: 1.5;
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
            top: 25%;
            left: 10%;
            width: 80%;
            opacity: 0.05;
            z-index: -1;
        }

        .content {
            padding: 25px 50px 80px 50px;
            position: relative;
            z-index: 1;
        }

        h3, h4 {
            text-align: center;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background: #f1f1f1;
            padding: 6px 10px;
            font-weight: bold;
            border-left: 4px solid #333;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f8f8f8;
        }

        .small {
            color: #555;
            font-size: 11pt;
        }

        .generated-date {
            position: fixed;
            bottom: 30px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 10pt;
            color: #555;
            font-style: italic;
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
        <h4>APPLICATION DETAILS</h4>

        <div class="section">
            <div class="section-title">Basic Information</div>
            <p><strong>Name:</strong> {{ $application->user->full_name }}</p>
            <p><strong>Email:</strong> {{ $application->user->email }}</p>
            <p><strong>Application Type:</strong> {{ $application->applicationSetting->name }}</p>
            <p><strong>Academic Session:</strong> {{ $application->academic_session }}</p>
            <p><strong>Status:</strong> {{ $application->submitted_by ? 'Submitted' : 'Not Submitted' }}</p>
        </div>

        @if(!empty($modules['profile']) && $modules['profile'])
            <div class="section">
                <div class="section-title">Profile</div>
                @if($application->profile)
                    <p><strong>Date of Birth:</strong> {{ $application->profile->date_of_birth ?? 'N/A' }}</p>
                    <p><strong>Gender:</strong> {{ $application->profile->gender ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $application->profile->address ?? 'N/A' }}</p>
                    <p><strong>State of Origin:</strong> {{ $application->profile->state_of_origin ?? 'N/A' }}</p>
                    <p><strong>Nationality:</strong> {{ $application->profile->nationality ?? 'N/A' }}</p>
                @else
                    <p class="small">No profile data submitted.</p>
                @endif
            </div>
        @endif

        @if(!empty($modules['olevel']) && $modules['olevel'])
            <div class="section">
                <div class="section-title">O’Level Results</div>
                @foreach($application->olevels as $olevel)
                    <p><strong>{{ $olevel->exam_type }} - {{ $olevel->exam_year }}</strong></p>
                    <table>
                        <thead>
                            <tr><th>Subject</th><th>Grade</th></tr>
                        </thead>
                        <tbody>
                            @forelse($olevel->subjects as $subject => $grade)
                                <tr>
                                    <td>{{ $subject }}</td>
                                    <td>{{ $grade }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="small">No subjects recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endforeach
            </div>
        @endif

        @if(!empty($modules['jamb_detail']) && $modules['jamb_detail'])
            <div class="section">
                <div class="section-title">JAMB Details</div>
                @if($application->jambDetail)
                    <p><strong>Reg No:</strong> {{ $application->jambDetail->registration_number }}</p>
                    <p><strong>Exam Year:</strong> {{ $application->jambDetail->exam_year }}</p>
                    <p><strong>Type:</strong> {{ $application->jambDetail->jamb_type }}</p>
                    <p><strong>Total Score:</strong> {{ $application->jambDetail->score ?? 'N/A' }}</p>

                    @if(!empty($application->jambDetail->subject_scores))
                        <table>
                            <thead><tr><th>Subject</th><th>Score</th></tr></thead>
                            <tbody>
                                @foreach($application->jambDetail->subject_scores as $subject => $score)
                                    <tr>
                                        <td>{{ $subject }}</td>
                                        <td>{{ $score }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @else
                    <p class="small">No JAMB details provided.</p>
                @endif
            </div>
        @endif

        @if(!empty($modules['course_of_study']) && $modules['course_of_study'])
            <div class="section">
                <div class="section-title">Course of Study</div>
                <p><strong>First Choice:</strong> {{ $application->user->courseOfStudy?->firstDepartment?->department_name ?? 'N/A' }}</p>
                <p><strong>Second Choice:</strong> {{ $application->user->courseOfStudy?->secondDepartment?->department_name ?? 'N/A' }}</p>
            </div>
        @endif

        @if(!empty($modules['education_history']) && $modules['education_history'])
            <div class="section">
                <div class="section-title">Education History</div>
                @forelse($application->educationHistories as $edu)
                    <p>
                        <strong>{{ $edu->institution_name }}</strong>  
                        ({{ $edu->start_date }} - {{ $edu->end_date }})<br>
                        Qualification: {{ $edu->qualification ?? 'N/A' }}<br>
                        Grade: {{ $edu->grade ?? 'N/A' }}
                    </p>
                @empty
                    <p class="small">No education history submitted.</p>
                @endforelse
            </div>
        @endif

        @if(!empty($modules['documents']) && $modules['documents'])
            <div class="section">
                <div class="section-title">Documents</div>
                @forelse($application->documents as $doc)
                    <p><strong>{{ ucfirst($doc->type) }}</strong>: {{ $doc->original_name }}</p>
                @empty
                    <p class="small">No documents uploaded.</p>
                @endforelse
            </div>
        @endif
    </div>

    <!-- Generated Date -->
    <div class="generated-date">
        Generated on {{ \Carbon\Carbon::now()->format('F j, Y \a\t g:i A') }}
    </div>

    <!-- Footer -->
    <img src="{{ public_path('portal_assets/img/users/letter_head_footer.png') }}" class="footer">

</body>
</html>
