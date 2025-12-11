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
                            <img src="{{ asset('assets/img/team/dr-akeem.png') }}" alt="Dr. Akeem Oyewale">
                            
                            <h3 class="mt-3">Dr. Akeem Oyewale</h3>
                            <strong>Member, Governing Council</strong>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="team-details">
                            <h3 class="mt-3">Dr. Akeem Oyewale</h3>
                            <strong>Member, Governing Council</strong>
                            
                            <p class="mt-3">
                                Dr. Akeem Oyewale holds a B.Sc. (Hons) degree in Accounting from the University of Lagos, an MBA from Manchester Business School, United Kingdom, and a Doctorate Degree in Management of Technology and Innovation from Da Vinci Institute, South Africa. He is an alumnus of the Said Business School, University of Oxford, and a certified impact investing expert from the Graduate School of Business, University of Cape Town. He also holds a Professional Certificate in Islamic Capital Markets from INCEIF, Malaysia.
                            </p>
                            <p>
                                Akeem, an Arthur Anderson scholar, has over 20 years of extensive experience in investment banking, credit marketing, trade finance, and sustainable finance. He is a Fellow of the Institute of Chartered Accountants of Nigeria (ICAN), the Chartered Institute of Stockbrokers (CIS), and the Chartered Institute of Bankers of Nigeria (CIBN), and an Associate of the Chartered Institute of Taxation of Nigeria (CITN). Additionally, he is a member of the Pension Industry Non-Interest Advisory Committee (PINAC) and currently serves as the second Vice President of the Chartered Institute of Stockbrokers (CIS).
                            </p>
                            <p>
                                He previously served as CEO of Stanbic IBTC Stockbrokers Limited, Stanbic IBTC Asset Management Limited, and Stanbic IBTC Nominees Limited, all part of the Standard Bank Group. During his tenure, he managed the Stanbic IBTC Nigerian Equity Fund, Nigeria’s largest mutual fund at the time, and was responsible for the launch of the Stanbic IBTC Ethical Fund.
                            </p>
                            <p>
                                He is currently the Chief Executive Officer of Marble Capital Limited and a member of the Securities and Exchange Commission's (SEC) Financial Literacy Technical Committee. Under his leadership, Marble Capital Limited launched Nigeria’s first commodities fund (the Marble Halal Commodities Fund, MHCF) and the Marble Halal Fixed Income Fund (MHFIF).
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