<!DOCTYPE html>
<html lang="en">

@include('website.partials.head')

<body class="">

    <!-- header area -->
    @include('website.partials.menu')
    <!-- header area end -->




    <main class="main">

        <!-- breadcrumb -->
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">Contact Us</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="active">Contact Us</li>
                </ul>
            </div>
        </div>
        <!-- breadcrumb end -->


        <!-- contact area -->
        <div class="contact-area py-120">
            <div class="container">
                <div class="contact-content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="contact-info">
                                <div class="contact-info-icon">
                                    <i class="fal fa-map-location-dot"></i>
                                </div>
                                <div class="contact-info-content">
                                    <h5>Address</h5>
                                    <p><i class="far fa-location-dot"></i> Offa Kwara State, Nigeria.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="contact-info">
                                <div class="contact-info-icon">
                                    <i class="fal fa-phone-volume"></i>
                                </div>
                                <div class="contact-info-content">
                                    <h5>Call Us</h5>
                                    <p><a href="tel:+2348066814330"><i class="far fa-phone-volume"></i> +234 806 681
                                            4330</a> <a href="tel:+2348066552935">| +234 806 655 2935</a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="contact-info">
                                <div class="contact-info-icon">
                                    <i class="fal fa-alarm-clock"></i>
                                </div>
                                <div class="contact-info-content">
                                    <h5>Open Time</h5>
                                    <p>Mon - Fri (08.00AM - 05.30PM)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="contact-wrapper">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="contact-img">
                                <img src="assets/img/contact/01.jpg" alt="">
                            </div>
                        </div>
                        <div class="col-lg-7 align-self-center">
                            <div class="contact-form">
                                <div class="contact-form-header">
                                    <h2>Get In Touch</h2>
                                    <p>
                                        The University of Offa is committed to providing quality education that inspires innovation,
                                        nurtures character, and prepares students for leadership roles in society.
                                        Our programs are designed to meet global standards while addressing local and national needs.
                                    </p>

                                </div>
                                <form method="post" action="https://live.themewild.com/eduka/assets/php/contact.php" id="contact-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="name"
                                                    placeholder="Your Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="email" class="form-control" name="email"
                                                    placeholder="Your Email" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="subject"
                                            placeholder="Your Subject" required>
                                    </div>
                                    <div class="form-group">
                                        <textarea name="message" cols="30" rows="5" class="form-control" placeholder="Write Your Message"></textarea>
                                    </div>
                                    <button type="submit" class="theme-btn">Send
                                        Message <i class="far fa-paper-plane"></i></button>
                                    <div class="col-md-12 mt-3">
                                        <div class="form-messege text-success"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
        <!-- end contact area -->

        <!-- map -->
        <div class="contact-map">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3933.3292164000896!2d4.721121515326307!3d8.1475628941374!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1036a112dbb36f01%3A0xd0c2c8eb87e2f3cb!2sOffa%2C%20Kwara%20State!5e0!3m2!1sen!2sng!4v1694277563450!5m2!1sen!2sng"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>


    </main>



    <!-- footer area -->
    @include('website.partials.footer')
    <!-- footer area end -->

    <!-- scroll-top -->
    <a href="contact.html#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>
    <!-- scroll-top end -->


    <!-- js -->
    @include('website.partials.js')

</body>

</html>
