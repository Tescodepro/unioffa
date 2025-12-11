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
                            <img src="{{ asset('assets/img/team/mr-oyewale.jpg') }}" alt="Mr. Oyewale Opeyemi Abdulateef">
                            
                            <h3 class="mt-3">Mr. Oyewale O. Abdulateef</h3>
                            <strong>Member, Governing Council</strong>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="team-details">
                            <h3 class="mt-3">Mr. Oyewale Opeyemi Abdulateef</h3>
                            <strong>Executive Chairman, Oyewale Empire Limited</strong>
                            
                            <p class="mt-3">
                                Mr. Oyewale Opeyemi Abdulateef is a native of Kwara State, hailing from the Oyewale Royal Family. He is the Executive Chairman of Oyewale Empire Limited and the Chairman of the Board of Trustees at Ikirun College of Health Technology, Ikirun in Osun State. Additionally, he serves as an Educational Consultant and is a Member of the Governing Council of the University of Offa, Kwara State.
                            </p>
                            <p>
                                He started his career as an Accountant at the College of Education Ilemona, Kwara State, before securing an appointment at OVH Energy (Oando Licensee). During his tenure of over ten years with OVH Energy, he worked in various departments within the company, including the Banking Operations Department, Lead Aviation, and the Lubricant Department.
                            </p>
                            <p>
                                He holds a B.Sc. in Accounting from Fountain University, Osogbo, and a Masters in Business Administration (MBA) from Obafemi Awolowo University, Ile-Ife. He is also an Associate Chartered Accountant (ACA) under the prestigious Institute of Chartered Accountants of Nigeria (ICAN).
                            </p>
                            <p>
                                He is happily married and blessed with children.
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