<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Letter</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
            position: relative;
            font-size: 13pt;
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
            padding: 15px 30px 30px 30px;
            position: relative;
            z-index: 1;
        }
        
        .student-info {
            margin: 20px 0;
            line-height: 1.5;
        }
        
        .student-info span {
            display: block;
            margin-bottom: 3px;
        }
        
        h4 {
            text-align: center;
            text-decoration: underline;
            margin: 25px 0 20px 0;
            font-weight: bold;
            font-size: 15pt;
        }
        
        p {
            text-align: justify;
            margin: 12px 0;
            line-height: 1.4;
        }
        
        ol {
            margin: 15px 0;
            padding-left: 20px;
        }
        
        ol li {
            margin: 10px 0;
            text-align: justify;
            line-height: 1.4;
        }
        
        ul {
            margin: 8px 0;
            padding-left: 18px;
        }
        
        ul li {
            margin: 5px 0;
            line-height: 1.3;
        }
        
        .signature-block {
            margin-top: 35px;
            text-align: left;
            page-break-inside: avoid;
        }
        
        .signature-block img {
            width: 180px;
            display: block;
            margin-left: 0;
            margin-right: auto;
        }
        
        .signature-name {
            margin-top: 10px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14pt;
        }
        
        .signature-title {
            margin-top: 0;
            font-style: italic;
            font-size: 13pt;
        }
        
        strong {
            font-weight: bold;
        }
        
        /* Print styles */
        @media print {
            body {
                font-size: 12pt;
            }
            
            .content {
                padding: 10px 50px 100px 50px;
            }
            
            .signature-block {
                margin-top: 10px;
            }
        }
        
        @page {
            margin: 0;
            size: A4;
        }
    </style>
</head>
<body>

    @php
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $durationInWords = $formatter->format($duration->admission_duration);
    @endphp

    <!-- Letterhead -->
    <img src="{{ public_path('portal_assets/img/users/letter_head.png') }}" class="letterhead">

    <!-- Watermark -->
    <img src="{{ public_path('portal_assets/img/users/letter_head.png') }}" class="watermark">

    <div class="content">
        <div class="student-info">
            <span><strong>Applicant Name:</strong> {{ strtoupper(auth()->user()->full_name) }}</span>
            <span><strong>Gender:</strong> {{ strtoupper($student->sex ?? '---') }}</span>
            <span><strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</span>
        </div>

        <h4>OFFER OF PROVISIONAL ADMISSION FOR {{ $student->admission_session }}</h4>

        <p>
            Consequent upon your application, you are hereby offered provisional admission into the 
            University for a {{ ucfirst($durationInWords) }} ({{ $duration->admission_duration }}) year Programme leading to the award of 

            <strong>
                @if($department->qualification == 'B.Ed' || $department->qualification == 'BEd')
                    Bachelor of Education
                @else
                    Bachelor of Science
                @endif
                ({{ $department->qualification }}) {{ strtoupper($department->department_name) }}
            </strong>.

        </p>

        <p>The following conditions are expected to be met in respect to your admission:</p>

        <ol>
            <li>On arrival, present the following:
                <ul>
                    @if ($student->programme != 'TOPUP')
                        <li>The Originals of your certificates</li>
                        <li>Authentic JAMB Result slip</li>
                        <li>Authentic JAMB Admission Letter</li>
                    @elseif($student->programme == 'TOPUP')
                        <li>Original certificate(s) of National Youth Service Corps (NYSC) discharge or exemption certificate</li>
                        <li>Original certificate(s) of HND , OND or Bachelor Degree(s) in the relevant field</li>
                        <li>Original certificate(s) of Olevel with at least five (5) credit passes including English Language and Mathematics</li>
                    @endif
                    <li>Original Birth Certificate or statutory declaration of age</li>
                    <li>Four copies of your recent passport-size photographs</li>
                </ul>
            </li>
            <li>
                The University has the right to withdraw your admission if it is discovered at any time 
                that you do not possess the entry requirement upon which the admission was granted.
            </li>
            <li>
                The University shall also withdraw your admission if it is discovered at any time that 
                you are involved in any unwholesome behavior or gross misconduct.
            </li>
            <li>
                You are to present a letter of attestation from a reputable person that you will be of 
                good behavior during your studentship.
            </li>
        </ol>

        <p style="margin-top: 10px;">Accept my warmest congratulations on your Admission.</p>

        <!-- Signature Block -->
        <div class="signature-block">
            <img src="{{ public_path('portal_assets/img/users/signature.png') }}" alt="Registrar's Signature" style="height: 20px;">
            <p class="signature-name">Mr. Salaudeen OYEWALE</p>
            <p class="signature-title">Ag. Registrar</p>
        </div>
    </div>

    <!-- Footer -->
    <img src="{{ public_path('portal_assets/img/users/letter_head_footer.png') }}" class="footer">

</body>
</html>