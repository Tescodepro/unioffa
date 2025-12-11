<!DOCTYPE html>
<html lang="en">

@include('website.partials.head')

<body class="">

    @include('website.partials.menu')
    <main class="main">
        <div class="site-breadcrumb" style="background: url('{{ asset('assets/img/cta/01.jpg') }}')">
            <div class="container">
                <h2 class="breadcrumb-title">Pro-Chancellor</h2>
                <h4 class="text-white" style="font-size: 30px">University of Offa, Offa, Kwara State, Nigeria</h4>
                <ul class="breadcrumb-menu">
                    <li><a href="index.html">Home</a></li>
                    <li class="active">Pro-Chancellor</li>
                </ul>
            </div>
        </div>
        <div class="team-single pt-120 pb-80">
            <div class="container">
                <div class="row align-items-start">
    
    <div class="col-md-4">
        <div class="team-single-img">
            <img src="{{ asset('assets/img/team/prof-goke.jpeg') }}" alt="Prof Goke Lalude">
            <h3 class="mt-3">Prof Goke Lalude</h3>
            <strong>The Pro-Chancellor</strong>
        </div>
    </div>

    <div class="col-md-8">
        <div class="team-details">
            <h3 class="mt-3">Prof Goke Lalude</h3>
            <strong>The Pro-Chancellor</strong>
            <p class="mt-3">

                Prof Goke Lalude was born in Ogbomosho on the 3rd of December, 1963. He attended Alafia Institute, Mokola Ibadan where he attained his Nursery and Primary school education between 1968 and 1974, before proceeding to Loyola College, within same Ibadan for his secondary school education between 1974 and 1979, from where he moved to Olivet Baptist High School, Oyo for the Higher School Certificate, obtained in 1981.
            </p>
            <p>
                He was at the University of Ife for a First Degree in History, graduating with a Second Class Upper Division in 1984. He was back at the University of Ibadan for his Masters Degree in Political Science immediately after the mandatory national service and graduated in 1986, and a PhD in 2006.
            </p>
            <p>
                Before commencing a career in academics as an Assistant Lecturer at the then Ogun State University in 1996, he was at the Federal Ministry of Information and Culture between 1987 and 1996. He was appointed a Professor at the Fountain University, Osogbo in 2015. Professor Lalude has been Head of Political Science at the Olabisi Onabanjo University and Fountain University, where he was also Dean, College of Management and Social Sciences. He was pioneer Dean of the Postgraduate School of Fountain University Osogbo, where he is currently Director of Academic Planning.
            </p>
            <p>
                Prof Lalude has been an External Examiner at the Undergraduate level in University of Ilorin, Landmark University and Summît University. He has equally examined PhD Theses at the Babcock University, Tai Solarin University of Education and Lead City University. He has assessed Professors at Ambrose Ali University, Caleb University, Afe Babalola University, Adeleke University and Lagos State University. He has also been member and Chairman of National Universities Commission panel on Accreditation to various universities from 2019 till date.
            </p>
            <p>
                He is a political analyst on Radio and Television as well as the print media. A family man, Professor Goke Lalude is married to Kemi and the marriage is blessed with three daughters and a grandson.
            </p>

            {{-- <div class="team-details-info">
                <ul>
                    <li><a href="#"><i class="far fa-location-dot"></i> Fountain University, Osogbo, Nigeria</a></li>
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