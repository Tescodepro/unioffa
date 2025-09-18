<header class="header">
        <!-- header top -->
        <div class="header-top">
            <div class="container ps-0">
                <div class="header-top-wrap">
                    <div class="header-top-left">
                        <div class="header-top-social">
                            <span>Follow Us: </span>
                            <a href=""><i class="fab fa-facebook-f"></i></a>
                            <a href=""><i class="fab fa-instagram"></i></a>
                            <a href=""><i class="fab fa-youtube"></i></a>
                            <a href=""><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                    <div class="header-top-right">
                        <div class="header-top-contact">
                            <ul>
                                <li>
                                    <a href=""><i class="far fa-location-dot"></i> Offa Kwara State, Nigeria.</a>
                                </li>
                                <li>
                                     <a href="tel:+2348066814330"><i class="far fa-phone-volume"></i> +234 806 681 4330</a> <a href="tel:+2348066552935">|  +234 806 655 2935</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="main-navigation">
            <nav class="navbar navbar-expand-lg">
                <div class="container position-relative">
                    <a class="navbar-brand" href="{{route('home')}}">
                        <img src="{{ asset('assets/img/logo/logo.jpeg')}}" alt="logo" style="width: 80px; height: auto;">
                    </a>
                    <div class="mobile-menu-right">
                        <div class="search-btn">
                            <button type="button" class="nav-right-link search-box-outer"><i
                                    class="far fa-search"></i></button>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#main_nav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-mobile-icon"><i class="far fa-bars"></i></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="main_nav">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link active" href="{{route('home')}}">Home</a>
                            </li>
                            
                            <li class="nav-item mega-menu dropdown">
                                <a class="nav-link dropdown-toggle"  data-bs-toggle="dropdown">About UniOffa</a>
                                <div class="dropdown-menu fade-down">
                                    <div class="mega-content">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-12 col-sm-4 col-md-3">
                                                    <h5>About Us</h5>
                                                    <div class="menu-about">
                                                        <a href="{{route('home')}}" class="menu-about-logo"><img
                                                                src="{{ asset('assets/img/logo/logo.jpeg')}}" alt="" style="width: 80px; height: auto;"></a>
                                                        <p> At University of Offa we are committed to shaping bright futures through innovative teaching, impactful research, and a nurturing academic environment that prepares students for global opportunities.</p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4 col-md-3">
                                                    <h5>Who we are</h5>
                                                    <ul class="mega-menu-item">
                                                        <li><a class="dropdown-item" href="">Our History</a></li>
                                                        <li><a class="dropdown-item" href="">Our Phylosophy</a></li>
                                                        <li><a class="dropdown-item" href="">Our Core Value</a></li>
                                                    </ul>
                                                </div>
                                                <div class="col-12 col-sm-4 col-md-3">
                                                    <h5>Meet Our Tea,</h5>
                                                    <ul class="mega-menu-item">
                                                        <li><a class="dropdown-item"
                                                                href="">Chancellor</a></li>
                                                        <li><a class="dropdown-item" href="">Board of Trustees</a></li>
                                                        <li><a class="dropdown-item" href="">Governing Council</a></li>
                                                        <li><a class="dropdown-item" href="">Principal Officers Committee</a></li>
                                                        <li><a class="dropdown-item" href="">Management Committee</a></li>
                                                    </ul>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-3">
                                                    <h5>University Campuses</h5>
                                                    <ul class="mega-menu-item">
                                                        <li><a class="dropdown-item" href="">Igbeti Campus </a></li>
                                                        <li><a class="dropdown-item" href="">DSAP Ogun State Campus</a></li>
                                                        <li><a class="dropdown-item" href="">Ilorin Campus</a></li>
                                                        
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="" data-bs-toggle="dropdown">Academics</a>
                                <ul class="dropdown-menu fade-down">
                                    <li><a class="dropdown-item" href="">Faculty</a></li>
                                    <li><a class="dropdown-item" href="">Academic Calendar</a></li>
                                    <li><a class="dropdown-item" href="">Academic Programme</a></li>
                                </ul>
                            </li>
                            <!-- <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="" data-bs-toggle="dropdown">Pages</a>
                                <ul class="dropdown-menu fade-down">
                                    <li><a class="dropdown-item" href="about.html">About Us</a></li>
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="">Events</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="event.html">Events</a></li>
                                            <li><a class="dropdown-item" href="event-single.html">Event Single</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="">Portfolio</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="portfolio.html">Portfolio</a></li>
                                            <li><a class="dropdown-item" href="portfolio-single.html">Portfolio
                                                    Single</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="">Teachers</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="teacher.html">Teachers One</a></li>
                                            <li><a class="dropdown-item" href="teacher-2.html">Teachers Two</a></li>
                                            <li><a class="dropdown-item" href="teacher-single.html">Teachers Single</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item" href="gallery.html">Gallery</a></li>
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="">Authentication</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="login.html">Login</a></li>
                                            <li><a class="dropdown-item" href="register.html">Register</a></li>
                                            <li><a class="dropdown-item" href="forgot-password.html">Forgot Password</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item" href="pricing.html">Pricing Plan</a></li>
                                    <li><a class="dropdown-item" href="faq.html">Faq</a></li>
                                    <li><a class="dropdown-item" href="testimonial.html">Testimonials</a></li>
                                    <li><a class="dropdown-item" href="404.html">404 Error</a></li>
                                    <li><a class="dropdown-item" href="coming-soon.html">Coming Soon</a></li>
                                    <li><a class="dropdown-item" href="terms.html">Terms Of Service</a></li>
                                    <li><a class="dropdown-item" href="privacy.html">Privacy Policy</a></li>
                                </ul>
                            </li> -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="" data-bs-toggle="dropdown">Admissions</a>
                                <ul class="dropdown-menu fade-down">
                                    <li><a class="dropdown-item" href="">How To Apply</a></li>
                                    <li><a class="dropdown-item" href="">Application Form</a></li>
                                    <li><a class="dropdown-item" href="">Tuition Fees</a></li>
                                    <li><a class="dropdown-item" href="">Scholarships</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Support Units</a>
                                <ul class="dropdown-menu fade-down">
                                    <li><a class="dropdown-item" href="">Academic Planning Unit</a></li>
                                    <li><a class="dropdown-item" href="">Admissions</a></li>
                                    <li><a class="dropdown-item" href="">Bursary</a></li>
                                    <li><a class="dropdown-item" href="">Communication & Marketing</a></li>
                                    <li><a class="dropdown-item" href="">General Studies Unit</a></li>
                                    <li><a class="dropdown-item" href="">ICT</a></li>
                                    <li><a class="dropdown-item" href="">Internal Audit</a></li>
                                    <li><a class="dropdown-item" href="">Office of the Vice-Chancellor</a></li>
                                    <li><a class="dropdown-item" href="">Physical Planning & Development</a></li>
                                    <li><a class="dropdown-item" href="">Registry</a></li>
                                    <li><a class="dropdown-item" href="">Security</a></li>
                                    <li><a class="dropdown-item" href="">Student Care Services</a></li>
                                    <li><a class="dropdown-item" href="">TAU Ventures</a></li>
                                    <li><a class="dropdown-item" href="">University Health Services</a></li>
                                    <li><a class="dropdown-item" href="">University Library</a></li>
                                </ul>
                            </li>

                            <li class="nav-item"><a class="nav-link" href="{{route('contact')}}">Contact</a></li>
                        </ul>
                        <div class="nav-right">
                            <div class="nav-right-btn mt-2">
                                <a href="" class="theme-btn"><span
                                        class="fal fa-pencil"></span>Apply Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>