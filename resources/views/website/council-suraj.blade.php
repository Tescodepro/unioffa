<!DOCTYPE html>
<html lang="en">

@include('website.partials.head')

<body class="">

    @include('website.partials.menu')
    <main class="main">
        <div class="site-breadcrumb" style="background: url('{{ asset('assets/img/cta/01.jpg') }}')">
            <div class="container">
                <h2 class="breadcrumb-title">Member, Governing Council</h2>
                <h4 class="text-white" style="font-size: 30px">University of Offa, Offa, Kwara State, Nigeria</h4>
                <ul class="breadcrumb-menu">
                    <li><a href="index.html">Home</a></li>
                    <li class="active">Council Member</li>
                </ul>
            </div>
        </div>
        <div class="team-single pt-120 pb-80">
            <div class="container">
                <div class="row align-items-start">
    
                    <div class="col-md-4">
                        <div class="team-single-img">
                            <img src="{{ asset('assets/img/team/mr-suraj.jpeg') }}" alt="Mr. Suraj Oyewale">
                            
                            <h3 class="mt-3">Mr. Suraj Oyewale</h3>
                            <strong>Member, Governing Council</strong>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="team-details">
                            <h3 class="mt-3">Mr. Suraj Oyewale, FCA, MBA</h3>
                            <strong>Member, Governing Council</strong>
                            
                            <p class="mt-3">
                                Suraj holds a first-class degree in Economics from the Obafemi Awolowo University (OAU), Ile Ife, an MBA from the Edinburgh Business School, Scotland, and a certificate in Real Estate Economics and Finance from the London School of Economics and Political Science, England.
                            </p>
                            <p>
                                He is a Fellow of the Institute of Chartered Accountants of Nigeria (ICAN) and an Associate Member of the Chartered Institute of Taxation of Nigeria (CITN).
                            </p>
                            <p>
                                Suraj spent over 15 years working in Nigeria’s oil and gas and consulting industries, traversing companies like Oando Plc, Seven Energy International Limited, Savannah Energy Plc, and PricewaterhouseCoopers (PwC).
                            </p>
                            <p>
                                He resigned from PwC in 2023 to run his own companies, namely Jarus Homes and Investments Limited, Jarus Travel Services Limited, and Fortrose Consulting Limited.
                            </p>
                            <p>
                                Suraj is an author of four books, namely: <em>The Road to Victoria Island</em>, <em>How to Network Like a Pro</em>, <em>A Mat of Roses</em>, and <em>Wahala No Dey Finish</em>.
                            </p>

                            {{-- <div class="team-details-info">
                                <ul>
                                    <li><a href="#"><i class="far fa-location-dot"></i> University of Offa, Nigeria</a></li>
                                    <li><a href="#"><i class="far fa-envelope"></i> <span class="__cf_email__">[email&#160;protected]</span></a></li>
                                    <li><a href="#"><i class="far fa-phone"></i> +234 800 000 0000</a></li>
                                </ul>
                            </div>
                            <div class="team-details-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-whatsapp"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div> --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        </main>

    @include('website.partials.footer')
    <a href="#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>
    @include('website.partials.js')

</body>

</html>