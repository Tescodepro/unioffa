<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admission Letter - {{ strtoupper($student->full_name) }}</title>
    <style>
        /* A4 Page Definition */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
        }

        /* Letterhead - Positioned at top */
        .wrapper {
            width: 100%;
        }

        .letterhead {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10;
        }

        /* Footer - Positioned at bottom */
        .footer {
            width: 100%;
            position: fixed;
            bottom: 0px;
            left: 0;
            z-index: 10;
        }

        /* Watermark - Centered background */
        .watermark {
            position: fixed;
            top: 40%;
            left: 10%;
            width: 80%;
            opacity: 0.05;
            z-index: -1;
        }

        /* Main Content Container */
        .content {
            padding: 220px 25mm 40px 25mm;
            /* Top space for letterhead, sides for margins */
            font-size: 11pt;
            line-height: 1.35;
            position: relative;
            z-index: 5;
        }

        .student-info {
            margin-bottom: 10px;
        }

        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-info td {
            padding: 1px 0;
            vertical-align: top;
        }

        .student-info .label {
            font-weight: bold;
            width: 130px;
        }

        .date-block {
            text-align: right;
            margin-bottom: 5px;
        }

        h4 {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 11.5pt;
            margin: 10px 0 10px 0;
            text-decoration: underline;
            line-height: 1.3;
        }

        p {
            text-align: justify;
            margin-bottom: 8px;
        }

        ol {
            margin: 8px 0;
            padding-left: 20px;
        }

        ol li {
            margin-bottom: 6px;
            text-align: justify;
        }

        ul {
            margin-top: 4px;
            margin-bottom: 6px;
            padding-left: 30px;
        }

        ul li {
            margin-bottom: 2px;
            list-style-type: disc;
        }

        .congratulations {
            margin-top: 10px;
            font-weight: bold;
        }

        /* Signature Section */
        .signature-block {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .signature-line {
            width: 250px;
            border-top: 1px solid #000;
            margin-bottom: 4px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 2px;
        }

        .signature-title {
            font-style: italic;
            font-size: 10pt;
        }

        .signature-img {
            height: 50px;
            margin-bottom: -10px;
            display: block;
        }

        strong {
            font-weight: bold;
        }

        .department-highlight {
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>

<body>

    @php
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $durationInWords = $formatter->format($duration->admission_duration);
    @endphp

    <div class="wrapper">
        <!-- Letterhead -->
        <img src="{{ public_path('portal_assets/img/users/letter_head.png') }}" class="letterhead">

        <!-- Watermark -->
        <img src="{{ public_path('assets/img/logo/logo.jpeg') }}" class="watermark">

        <div class="content">
            <div class="date-block">
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
            </div>

            <div class="student-info">
                <table>
                    <tr>
                        <td class="label">Applicant Name:</td>
                        <td>{{ strtoupper($student->full_name) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Gender:</td>
                        <td>{{ strtoupper(optional($profile)->gender ?? '---') }}</td>
                    </tr>
                </table>
            </div>

            <h4>OFFER OF PROVISIONAL ADMISSION FOR {{ $application->academic_session }} ACADEMIC SESSION</h4>

            <p>
                Consequent upon your application, you are hereby offered provisional admission into the
                University for a <strong>{{ ucfirst($durationInWords) }} ({{ $duration->admission_duration }})</strong>
                year Programme
                leading to the award of

                <span class="department-highlight">
                    @if($department->qualification == 'B.Ed' || $department->qualification == 'BEd')
                        Bachelor of Education
                    @else
                        Bachelor of Science
                    @endif
                    ({{ $department->qualification }}) in {{ strtoupper($department->department_name) }}
                </span>.
            </p>

            <p>The following conditions are expected to be met in respect to your admission:</p>

            <ol>
                <li>On arrival, present the following for screening:
                    <ul>
                        <li>The Originals of your certificates/O'Level results</li>
                        <li>Authentic JAMB Result slip</li>
                        <li>Authentic JAMB Admission Letter</li>
                        <li>Original Birth Certificate or statutory declaration of age</li>
                        <li>Four copies of your recent passport-size photographs</li>
                    </ul>
                </li>
                <li>
                    The University reserves the right to withdraw your admission if it is discovered at any time
                    that you do not possess the entry requirement upon which the admission was granted.
                </li>
                <li>
                    The University shall also withdraw your admission if it is discovered at any time that
                    you are involved in any unwholesome behavior, examination malpractice, or gross misconduct.
                </li>
                <li>
                    You are required to present a letter of attestation from a reputable person, confirming that you
                    will be of
                    good behavior during your studentship.
                </li>
            </ol>

            <p class="congratulations">Please accept my warmest congratulations on your Admission.</p>

            <!-- Signature Block -->
            <div class="signature-block">
                {{-- If signature image exists, display it --}}
                {{-- <img src="{{ public_path('portal_assets/img/users/signature.png') }}" class="signature-img"> --}}

                <div class="signature-line"></div>
                <p class="signature-name">Mr. Salaudeen OYEWALE</p>
                <p class="signature-title">Ag. Registrar</p>
            </div>
        </div>

        <!-- Footer -->
        <img src="{{ public_path('portal_assets/img/users/letter_head_footer.png') }}" class="footer">
    </div>

</body>

</html>