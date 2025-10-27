<!DOCTYPE html>
<html lang="en">

@include('website.partials.head')

<body>

    <!-- header area -->
    @include('website.partials.menu')
    <!-- header area end -->

    <main class="main">

        <!-- breadcrumb -->
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">2025/2026 Scholarship Information</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="active">Scholarship Information</li>
                </ul>
            </div>
        </div>
        <!-- breadcrumb end -->

        <!-- scholarship section -->
        <section class="affiliate-intro py-80 mt-5 mb-5">
            <div class="container">
                <div class="intro-text">
                    <div class="row">
                        <div class="col-md-12">
                            <h2>University Scholarship for 2025/2026 Academic Session</h2>
                            <p class="mt-3">
                                The University is pleased to announce its <strong>Scholarship Programme</strong> for the
                                2025/2026 academic session.  
                                This scholarship is designed to support <strong>newly admitted students</strong> who
                                demonstrate academic excellence and commitment to their studies.
                            </p>

                            <h4 class="mt-5">Eligibility Criteria</h4>
                            <p class="mt-3">
                                To be eligible for this scholarship, applicants must be <strong>new students</strong>
                                admitted into the university for the <strong>2025/2026 academic session</strong>.
                                Students entering through any of the following admission routes are considered:
                            </p>

                            <ul class="text-start mx-auto">
                                <li>1. <strong>UTME Applicants:</strong> Must have a JAMB score of <strong>200 or above.</strong></li>
                                <li>2. <strong>Direct Entry Applicants:</strong> Must possess a <strong>National Diploma (ND)</strong> with at least a <strong>Distinction</strong> or <strong>Upper Credit</strong>.</li>
                                <li>3. <strong>JUPEB / IJMB / Remedial Students:</strong> Must have a minimum grade of <strong>6.0 out of 12.0</strong>.</li>
                            </ul>

                            <p class="mt-3">
                                Please note that the scholarship is <strong>not available</strong> for returning students
                                or applicants from previous academic sessions.
                            </p>

                            <h4 class="mt-5">Application Process</h4>
                            <p class="mt-3">
                                The scholarship is automatically linked to your admission process. Follow the steps
                                below to be considered:
                            </p>

                            <ol class="text-start mx-auto">
                                <li>1. Apply for admission into the University for the 2025/2026 academic session through your preferred entry route (UTME, Direct Entry, JUPEB, IJMB, or Remedial).</li>
                                <li>2. Ensure that you meet the minimum academic requirements listed above.</li>
                                <li>3. Pay both the <strong>Application Fee</strong> and <strong>Acceptance Fee</strong> as part of your admission process.</li>
                                <li>4. Once your admission is confirmed, your eligibility for the scholarship will be automatically reviewed based on your submitted results and academic credentials.</li>
                                <li>5. Successful applicants will receive an official email notification with further details about the scholarship award and disbursement process.</li>
                            </ol>

                            <p class="mt-4">
                                There is <strong>no separate scholarship form</strong> to fill out — eligibility is
                                determined based on your admission records and academic performance.
                            </p>

                            <h4 class="mt-5">Important Notes</h4>
                            <ul class="text-start mx-auto">
                                <li>1. All applicants must ensure that their contact information (email and phone number)
                                    is accurate during admission registration.</li>
                                <li>2. Payment of both the application and acceptance fees is compulsory for scholarship
                                    consideration.</li>
                                <li>3. Any falsified or misleading academic records will lead to automatic
                                    disqualification.</li>
                            </ul>

                            <p class="mt-4">
                                For more information or clarification about the scholarship process, please contact the
                                Admissions Office or visit the official university website.
                            </p>

                            <div class="mt-5">
                                <a href="{{ route('application.register') }}" class="theme-btn">
                                    Proceed to Admission Form <i class="fas fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <!-- scholarship section end -->
    </main>

    <!-- footer area -->
    @include('website.partials.footer')
    <!-- footer area end -->

    <!-- scroll-top -->
    <a href="#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>
    <!-- scroll-top end -->

    <!-- js -->
    @include('website.partials.js')

</body>

</html>
