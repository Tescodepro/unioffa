<!DOCTYPE html>
<html lang="en">

@include('website.partials.head')

<body class="">
    @include('website.partials.menu')
    <main class="main">
        <div class="site-breadcrumb" style="background: url('{{ asset('assets/img/cta/01.jpg') }}')">
            <div class="container">
                <h2 class="breadcrumb-title">Registrar</h2>
                <h4 class="text-white" style="font-size: 30px">University of Offa, Offa, Kwara State, Nigeria</h4>
                <ul class="breadcrumb-menu">
                    <li><a href="index.html">Home</a></li>
                    <li class="active">Registrar</li>
                </ul>
            </div>
        </div>
        <div class="team-single pt-120 pb-80">
            <div class="container">
                <div class="row align-items-start">
                    <div class="col-md-4">
                        <div class="team-single-img">
                            <img src="{{ asset('assets/img/team/registrar.jpeg') }}" alt="Prof Goke Lalude">
                            <h3 class="mt-3">Oyewale Salaudeen Ajibola</h3>
                            <strong>Registrar, University of Offa</strong>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="team-details">
                            <h3>Oyewale Salaudeen Ajibola</h3>
                            <strong>Registrar, University of Offa</strong>
                            
                            <p class="mt-3">
                                Oyewale Salaudeen Ajibola was born in Offa, Kwara State, to a family that valued education and integrity. From a young age, his inquisitive mind and commitment to learning set him apart. After completing his secondary education as the best student in his secondary school WAEC during his set, he set his sights on higher education.
                            </p>
                            <p>
                                His pursuit of knowledge led him to Fountain University. His dedication to his studies culminated in a well-deserved second-class upper degree in his Department during his time at Fountain University, where he was the Best graduating student of his department. Salaudeen’s leadership skills and commitment to his peers earned him recognition and respect. He holds an M.Sc. from Kwara State University, Malete, Kwara State.
                            </p>
                            <p>
                                He was appointed as the Pioneer Rector of the Polytechnic Ojoku in 2020. Due to his diligence and handwork, Salaudeen showcased his administrative prowess and leadership capabilities, contributing to the institution's growth and success. His strategic thinking and commitment to fostering an environment of excellence made him an invaluable asset to the institution.
                            </p>

                            {{-- <div class="team-details-info">
                                <ul>
                                    <li><a href="#"><i class="far fa-location-dot"></i> The Polytechnic Ojoku, Kwara State</a></li>
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