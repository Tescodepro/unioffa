<!DOCTYPE html>
<html lang="en">

@include('website.partials.head')

<body class="">

    <!-- header area -->
    @include('website.partials.menu')
    <!-- header area end -->

    <main class="main">
        <!-- hero slider -->
        <div class="hero-section">
            <div class="hero-slider owl-carousel owl-theme">
                <!-- Slide 1 -->
                <div class="hero-single" style="background: url(assets/img/slider/slider-1.jpg)" loading="lazy">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-12 col-lg-8 mx-auto">
                                <div class="hero-content text-center">
                                    <h6 class="hero-sub-title" data-animation="fadeInDown" data-delay=".25s">
                                        <i class="far fa-book-open-reader"></i>Welcome to University of Offa
                                    </h6>
                                    <h1 class="hero-title" data-animation="fadeInRight" data-delay=".50s">
                                        Building a <span>Brighter</span> Future Through Education
                                    </h1>
                                    <p data-animation="fadeInLeft" data-delay=".75s">
                                        At the University of Offa, we are committed to academic excellence, research,
                                        and innovation that empower students to become future leaders in their chosen
                                        fields.
                                    </p>
                                    <div class="hero-btn justify-content-center" data-animation="fadeInUp"
                                        data-delay="1s">
                                        <a href="" class="theme-btn">Learn About Us <i
                                                class="fas fa-arrow-right-long"></i></a>
                                        <a href="" class="theme-btn theme-btn2">Get in Touch <i
                                                class="fas fa-arrow-right-long"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="hero-single" style="background: url(assets/img/slider/slider-2.jpg)" loading="lazy">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-12 col-lg-8 mx-auto">
                                <div class="hero-content text-center">
                                    <h6 class="hero-sub-title" data-animation="fadeInDown" data-delay=".25s">
                                        <i class="far fa-book-open-reader"></i>Welcome to University of Offa
                                    </h6>
                                    <h1 class="hero-title" data-animation="fadeInRight" data-delay=".50s">
                                        Nurturing Knowledge, Skills, and <span>Innovation</span>
                                    </h1>
                                    <p data-animation="fadeInLeft" data-delay=".75s">
                                        Join a dynamic learning community where students are inspired to explore,
                                        discover, and contribute to solving real-world challenges.
                                    </p>
                                    <div class="hero-btn justify-content-center" data-animation="fadeInUp"
                                        data-delay="1s">
                                        <a href="" class="theme-btn">Discover More <i
                                                class="fas fa-arrow-right-long"></i></a>
                                        <a href="" class="theme-btn theme-btn2">Our Programs <i
                                                class="fas fa-arrow-right-long"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="hero-single" style="background: url(assets/img/slider/slider-3.jpg)" loading="lazy">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-12 col-lg-8 mx-auto">
                                <div class="hero-content text-center">
                                    <h6 class="hero-sub-title" data-animation="fadeInDown" data-delay=".25s">
                                        <i class="far fa-book-open-reader"></i>Welcome to University of Offa
                                    </h6>
                                    <h1 class="hero-title" data-animation="fadeInRight" data-delay=".50s">
                                        Excellence in <span>Learning</span> and <span>Research</span>
                                    </h1>
                                    <p data-animation="fadeInLeft" data-delay=".75s">
                                        Our mission is to provide quality education and impactful research,
                                        shaping graduates who make meaningful contributions locally and globally.
                                    </p>
                                    <div class="hero-btn justify-content-center" data-animation="fadeInUp"
                                        data-delay="1s">
                                        <a href="" class="theme-btn">About Us <i
                                                class="fas fa-arrow-right-long"></i></a>
                                        <a href="{{ route('application.login') }}" class="theme-btn theme-btn2">Apply
                                            Now <i class="fas fa-arrow-right-long"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach($news->take(10) as $item)
                    <div class="hero-single" style="background: url('{{ asset('storage/'.$item->image) }}')">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-md-12 col-lg-8 mx-auto">
                                    <div class="hero-content text-center">
                                        <h6 class="hero-sub-title" data-animation="fadeInDown" data-delay=".25s">
                                            <i class="far fa-newspaper"></i> Latest News
                                        </h6>
                                        <h1 class="hero-title" data-animation="fadeInRight" data-delay=".50s">
                                            {{ Str::limit($item->title, 60) }}
                                        </h1>
                                        <p data-animation="fadeInLeft" data-delay=".75s">
                                            {{ Str::limit(strip_tags(html_entity_decode(html_entity_decode($item->content))), 120) }}
                                        </p>
                                        <div class="hero-btn justify-content-center" data-animation="fadeInUp" data-delay="1s">
                                            <a href="{{ route('news.show', $item->id) }}" class="theme-btn">
                                                Read More <i class="fas fa-arrow-right-long"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
        <!-- hero slider end -->

        <!-- about area -->
        <div class="about-area py-120">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5">
                        <div class="about-left wow fadeInLeft" data-wow-delay=".25s">
                            <div class="about-img">
                                <!-- <div class="row g-4"> -->
                                <div class="col-md-12">
                                    <img class="img-1" src="assets/img/about/vc.png" alt="" loading="lazy">
                                </div>
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="about-right wow fadeInRight" data-wow-delay=".25s">
                            <div class="site-heading mb-3">
                                <span class="site-title-tagline">
                                    <i class="far fa-book-open-reader"></i> Welcome Speech from the Vice Chancellor
                                </span>
                                <h4 class="vice_title">
                                    Welcome to <span>University of Offa</span>, Offa, Kwara State, Nigeria.
                                </h4>
                            </div>

                            <p class="about-text">
                                It is my pleasure to welcome you to the University of Offa, a center of excellence
                                dedicated to nurturing
                                knowledge, innovation, and character. At our university, we are committed to providing
                                quality education,
                                fostering research, and developing future leaders who will make positive contributions
                                to society.
                                Whether you are a prospective student, parent, partner, or visitor, we invite you to
                                explore the opportunities
                                and vibrant academic community that make the University of Offa unique.
                            </p>

                            <div class="mission-vision mt-4">
                                <div class="row">
                                    <!-- Mission -->
                                    <div class="col-md-6">
                                        <h5 class="fw-bold"><i class="fas fa-bullseye"></i> Our Mission</h5>
                                        <p>
                                            To provide world-class education, promote innovation and research, and
                                            cultivate a community
                                            of learners who are empowered with knowledge, skills, and values to
                                            positively transform society.
                                        </p>
                                    </div>

                                    <!-- Vision -->
                                    <div class="col-md-6">
                                        <h5 class="fw-bold"><i class="fas fa-lightbulb"></i> Our Vision</h5>
                                        <p>
                                            To be a leading institution recognized globally for academic excellence,
                                            groundbreaking research,
                                            and the holistic development of students into future leaders and
                                            change-makers.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- about area end -->

        <!-- faculty-area -->
        <div class="choose-area pt-80 pb-80">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Faculty Content -->
                    <div class="col-lg-8">
                        <div class="choose-content wow fadeInUp" data-wow-delay=".25s">
                            <div class="choose-content-info">
                                <div class="site-heading mb-0">
                                    <span class="site-title-tagline">
                                        <i class="far fa-book-open-reader"></i> Our Faculties
                                    </span>
                                    <h2 class="site-title text-white mb-10">
                                        Explore Our <span>Academic Faculties</span>
                                    </h2>
                                    <p class="text-white">
                                        At the University of Offa, our faculties are dedicated to fostering excellence
                                        in teaching,
                                        research, and innovation. Each faculty offers unique opportunities for students
                                        to build
                                        knowledge and skills for a successful future.
                                    </p>
                                </div>

                                <div class="choose-content-wrap">
                                    <div class="row g-4">

                                        <!-- Faculty of Education -->
                                        <div class="col-md-6">
                                            <div class="choose-item">
                                                <div class="choose-item-icon">
                                                    <img src="assets/img/icon/teacher-2.svg"
                                                        alt="Faculty of Education" loading="lazy">
                                                </div>
                                                <div class="choose-item-info">
                                                    <h4>Faculty of Education</h4>
                                                    <p>Preparing future educators and leaders with knowledge,
                                                        skills, and values for teaching and lifelong learning.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Faculty of Social and Management Sciences -->
                                        <div class="col-md-6">
                                            <div class="choose-item">
                                                <div class="choose-item-icon">
                                                    <img src="assets/img/icon/course-material.svg"
                                                        alt="Faculty of Social and Management Sciences">
                                                </div>
                                                <div class="choose-item-info">
                                                    <h4>Faculty of Social & Management Sciences</h4>
                                                    <p>Developing professionals in business, social sciences,
                                                        and management for global impact and leadership.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Faculty of Sciences and Computing -->
                                        <div class="col-md-6">
                                            <div class="choose-item">
                                                <div class="choose-item-icon">
                                                    <img src="assets/img/icon/online-course.svg"
                                                        alt="Faculty of Sciences and Computing" loading="lazy">
                                                </div>
                                                <div class="choose-item-info">
                                                    <h4>Faculty of Sciences & Computing</h4>
                                                    <p>Advancing science, technology, and innovation
                                                        through research and hands-on learning experiences.</p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Faculty Image -->
                    <div class="col-lg-4">
                        <div class="choose-img wow fadeInRight" data-wow-delay=".25s">
                            <img src="{{ asset('assets/img/choose/faculty.png') }}" alt="University Faculties"
                                loading="lazy" width="500" height="500">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- faculty-area end -->

        <!-- department area -->
        <div class="department-area py-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mx-auto">
                        <div class="site-heading text-center">
                            <span class="site-title-tagline"><i class="far fa-book-open-reader"></i>
                                Departments</span>
                            <h2 class="site-title">Browse Our <span>Departments</span></h2>
                            <p>Explore the various academic departments across our faculties, each dedicated to
                                excellence in teaching, research, and innovation.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Faculty of Social & Management Sciences -->
                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay=".25s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Accounting</a></h4>
                                <p>Training students in financial management, auditing, and taxation for professional
                                    excellence.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay=".50s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Business Administration</a></h4>
                                <p>Equipping students with managerial and entrepreneurial skills for leadership roles.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay=".75s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Economics</a></h4>
                                <p>Studying economic theories, policies, and applications to solve global challenges.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay="1s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Mass Communication</a></h4>
                                <p>Developing skills in journalism, broadcasting, and digital media communication.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay="1.25s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Political Science</a></h4>
                                <p>Focusing on governance, public policy, and international relations for societal
                                    impact.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty of Sciences & Computing -->
                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay=".25s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Physics with Electronics</a></h4>
                                <p>Combining physics principles with practical electronics applications.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay=".50s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Computer Science</a></h4>
                                <p>Training students in programming, data science, and artificial intelligence.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay=".75s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Cyber Security</a></h4>
                                <p>Equipping students to protect digital systems and data from cyber threats.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay="1s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Microbiology</a></h4>
                                <p>Studying microorganisms and their impact on health, industry, and the environment.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay="1.25s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Software Engineering</a></h4>
                                <p>Designing and building reliable, scalable, and innovative software systems.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay="1.5s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Biological Sciences</a></h4>
                                <p>Exploring life sciences, genetics, and biotechnology for human and environmental
                                    progress.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty of Education -->
                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay=".25s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Business Education</a></h4>
                                <p>Preparing educators with skills in commerce, management, and vocational training.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay=".50s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Social Studies Education</a></h4>
                                <p>Training teachers to promote civic responsibility, history, and social values.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay=".75s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Economic Education</a></h4>
                                <p>Preparing future educators to teach economics with practical and theoretical
                                    approaches.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="department-item wow fadeInUp" data-wow-delay="1s">
                            <div class="department-info">
                                <h4 class="department-title"><a href="">Biology Education</a></h4>
                                <p>Training educators in biological sciences to inspire the next generation of
                                    scientists.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- department area end -->

        <!-- gallery-area -->
        <!-- <div class="gallery-area py-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mx-auto">
                        <div class="site-heading text-center">
                            <span class="site-title-tagline"><i class="far fa-book-open-reader"></i> Gallery</span>
                            <h2 class="site-title">Our Photo <span>Gallery</span></h2>
                            <p>It is a long established fact that a reader will be distracted by the readable content of
                                a page when looking at its layout.</p>
                        </div>
                    </div>
                </div>
                <div class="row popup-gallery">
                    <div class="col-md-4 wow fadeInUp" data-wow-delay=".25s">
                        <div class="gallery-item">
                            <div class="gallery-img">
                                <img src="assets/img/gallery/01.jpg" alt="">
                            </div>
                            <div class="gallery-content">
                                <a class="popup-img gallery-link" href="assets/img/gallery/01.jpg"><i
                                        class="fal fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="gallery-item">
                            <div class="gallery-img">
                                <img src="assets/img/gallery/02.jpg" alt="">
                            </div>
                            <div class="gallery-content">
                                <a class="popup-img gallery-link" href="assets/img/gallery/02.jpg"><i
                                        class="fal fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wow fadeInUp" data-wow-delay=".50s">
                        <div class="gallery-item">
                            <div class="gallery-img">
                                <img src="assets/img/gallery/03.jpg" alt="">
                            </div>
                            <div class="gallery-content">
                                <a class="popup-img gallery-link" href="assets/img/gallery/03.jpg"><i
                                        class="fal fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="gallery-item">
                            <div class="gallery-img">
                                <img src="assets/img/gallery/04.jpg" alt="">
                            </div>
                            <div class="gallery-content">
                                <a class="popup-img gallery-link" href="assets/img/gallery/04.jpg"><i
                                        class="fal fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wow fadeInUp" data-wow-delay=".75s">
                        <div class="gallery-item">
                            <div class="gallery-img">
                                <img src="assets/img/gallery/05.jpg" alt="">
                            </div>
                            <div class="gallery-content">
                                <a class="popup-img gallery-link" href="assets/img/gallery/05.jpg"><i
                                        class="fal fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="gallery-item">
                            <div class="gallery-img">
                                <img src="assets/img/gallery/06.jpg" alt="">
                            </div>
                            <div class="gallery-content">
                                <a class="popup-img gallery-link" href="assets/img/gallery/06.jpg"><i
                                        class="fal fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- gallery-area end -->

        <!-- cta-area -->
        <div class="cta-area pt-50 pb-50">
            <div class="container">
                <div class="cta-wrapper">
                    <div class="row align-items-center">
                        <div class="col-lg-5 ms-lg-auto">
                            <div class="cta-content mt-0">
                                <h1>Admissions Now Open!</h1>
                                <p>Join the University of Offa and take the next step towards your future.
                                    Apply today and become part of a vibrant academic community dedicated to
                                    excellence in teaching, research, and innovation.</p>
                                <div class="cta-btn">
                                    <a href="{{ route('application.login') }}" class="theme-btn">Apply Now<i
                                            class="fas fa-arrow-right-long"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- cta-area end -->


        <!-- blog area -->
        <div class="blog-area py-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mx-auto">
                        <div class="site-heading text-center">
                            <span class="site-title-tagline"><i class="far fa-book-open-reader"></i> Our Blog</span>
                            <h2 class="site-title">Latest News & <span>Blog</span></h2>
                            <p>Stay updated with the latest news, insights, and articles from our university community.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @forelse($news as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="blog-item wow fadeInUp" data-wow-delay=".25s">

                                <div class="blog-date">
                                    <i class="fal fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('F d, Y') }}
                                </div>

                                <div class="blog-item-img">
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                                </div>

                                <div class="blog-item-info">
                                    <h4 class="blog-title">
                                        <a href="{{ route('news.show', $item->id) }}">{{ $item->title }}</a>
                                    </h4>
                                    <p>{!! html_entity_decode(html_entity_decode($item->short_title)) !!}</p>
                                    <a class="theme-btn" href="{{ route('news.show', $item->id) }}">
                                        Read More <i class="fas fa-arrow-right-long"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="col-lg-12">
                            <div class="text-center py-5">
                                <h4>No news available right now.</h4>
                                <p>Maybe refresh later? Something exciting might show up.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
        <!-- blog area end -->
    </main>

    @include('website.partials.footer')


    <!-- scroll-top -->
    <a href="index-2.html#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>
    <!-- scroll-top end -->
    <!-- js -->
    @include('website.partials.js')

</body>

</html>
