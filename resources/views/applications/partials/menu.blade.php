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
                            @guest
                                <li class="nav-item dropdown">
                                    <a class="nav-link active" href="{{route('home')}}">Home</a>
                                </li>
                            @endguest
                            
                            @auth
                                <li class="nav-item dropdown">
                                    <a class="nav-link active" href="{{route('application.dashboard')}}">Dashboard</a>
                                </li>
                            @endauth
                            
                        </ul>
                        
                        <div class="nav-right">
                            <div class="nav-right-btn mt-2">
                                @auth
                                    {{-- If user is logged in, show Logout --}}
                                    
                                    <a href="{{ route('application.logout') }}" class="theme-btn me-2">
                                        <span class="fal fa-sign-in"></span> Logout
                                    </a>
                                @else
                                    {{-- If not logged in, show Login & Register --}}
                                    <a href="{{ route('application.login') }}" class="theme-btn me-2">
                                        <span class="fal fa-sign-in"></span> Login
                                    </a>
                                    <a href="{{ route('application.register') }}" class="theme-btn">
                                        <span class="fal fa-user-plus"></span> Register
                                    </a>
                                @endauth
                            </div>
                        </div>


                    </div>
                </div>
            </nav>
        </div>
    </header>