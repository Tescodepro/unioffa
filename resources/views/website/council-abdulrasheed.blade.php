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
                            <img src="{{ asset('assets/img/team/abdulrasheed-oyewale.jpg') }}" alt="Abdulrasheed Oyewale">
                            
                            <h3 class="mt-3">Abdulrasheed Oyewale</h3>
                            <strong>Member, Governing Council</strong>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="team-details">
                            <h3 class="mt-3">Abdulrasheed Oyewale</h3>
                            <strong>Member, Governing Council</strong>
                            
                            <p class="mt-3">
                                Abdulrasheed Oyewale is a seasoned finance professional with an M.Sc. in Finance and a B.Sc. in Accounting. He brings over seven years of experience working in structured and corporate organizations, where he has honed his expertise in financial management.
                            </p>
                            <p>
                                He possesses a strong background in financial analysis, supervision, accounting, taxation, and financial reporting. Beyond his corporate experience, he has gained meaningful experience within the education sector, allowing him to bridge the gap between financial strategy and academic administration.
                            </p>
                            <p>
                                Abdulrasheed is committed to excellence and integrity. He is dedicated to supporting strategic growth and ensuring financial prudence as a member of the University Council.
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