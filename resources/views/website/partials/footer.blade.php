 <!-- footer area -->
    <footer class="footer-area">
        <div class="footer-shape">
            <img src="{{ asset('assets/img/shape/03.png') }}" alt="">
        </div>
        <div class="footer-widget">
            <div class="container">
                <div class="row footer-widget-wrapper pt-100 pb-70">
                    <div class="col-md-6 col-lg-4">
                        <div class="footer-widget-box about-us">
                            <a href="index-2.html#" class="footer-logo">
                                <img src='{{ asset("assets/img/logo/logo.jpeg") }}' alt="" style="width: 80px; height: auto;">
                            </a>
                            <p class="mb-3">
                                At the University of Offa, we are committed to academic excellence, research,
                                        and innovation that empower students to become future leaders in their chosen fields.
                            </p>
                            <ul class="footer-contact">
                                <li><a href="tel:+2349036154339"><i class="far fa-phone"></i>+234 903 615 4339</a></li>
                                <li><i class="far fa-map-marker-alt"></i>Irra road, Offa Kwara State, Nigeria.</li>
                                <li><a href=""><i class="far fa-envelope"></i>info@uniffa.edu.ng</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <div class="footer-widget-box list">
                            <h4 class="footer-widget-title">Quick Links</h4>
                            <ul class="footer-list">
                                <li><a href=""><i class="fas fa-caret-right"></i> About Us</a></li>
                                <li><a href="{{ route('application.login') }}"><i class="fas fa-caret-right"></i> Admission</a></li>
                                <li><a href="{{ route('agent.application') }}"><i class="fas fa-caret-right"></i> Affiliation</a></li>
                                <li><a href="{{ route('application.login') }}"><i class="fas fa-caret-right"></i> Student Portal</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-widget-box list">
                            <h4 class="footer-widget-title">Our Campus</h4>
                            <ul class="footer-list">
                                <li><a href=""><i class="fas fa-caret-right"></i> Events</a></li>
                                <li><a href=""><i class="fas fa-caret-right"></i> News</a></li>
                                <li><a href=""><i class="fas fa-caret-right"></i> Planning & Administration</a></li>
                                <li><a href=""><i class="fas fa-caret-right"></i> Office Of The Chancellor</a></li>
                                <li><a href=""><i class="fas fa-caret-right"></i> Facilities</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-widget-box list">
                            <h4 class="footer-widget-title">Newsletter</h4>
                            <div class="footer-newsletter">
                                <p>Subscribe Our Newsletter To Get Latest Update And News</p>
                                <div class="subscribe-form">
                                    <form action="">
                                        <input type="email" class="form-control" placeholder="Your Email">
                                        <button class="theme-btn" type="submit">
                                            Subscribe Now <i class="far fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="copyright-wrapper">
                    <div class="row">
                        <div class="col-md-6 align-self-center">
                            <p class="copyright-text">
                                &copy; Copyright <span id="date"></span> <a href="{{route('home')}}"> Unioffa </a> All Rights Reserved.
                            </p>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <ul class="footer-social">
                                <li><a href="index-2.html#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="index-2.html#"><i class="fab fa-whatsapp"></i></a></li>
                                <li><a href="index-2.html#"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a href="index-2.html#"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer area end -->