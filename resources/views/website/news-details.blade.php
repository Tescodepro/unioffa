<!DOCTYPE html>
<html lang="en">

@include('website.partials.head')

<body>

    @include('website.partials.menu')

    <main class="main">

        <!-- breadcrumb -->
        <div class="site-breadcrumb" style="background: url('{{ asset('storage/'.$news->image) }}')">
            <div class="container">
                <h2 class="breadcrumb-title">{{ $news->title }}</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="#">News</a></li>
                    <li class="active">{{ Str::limit($news->title, 30) }}</li>
                </ul>
            </div>
        </div>
        <!-- breadcrumb end -->

        <!-- News content section -->
        <section class="affiliate-intro py-80 mt-5 mb-5">
            <div class="container">
                <div class="intro-text">
                    <div class="row">
                        <div class="col-lg-8">

                            <div class="mb-4 text-muted">
                                <i class="far fa-calendar-alt me-2"></i>{{ $news->created_at->format('F d, Y') }}
                                @if($news->author ?? false)
                                    &nbsp;&nbsp;<i class="far fa-user-circle me-2"></i>{{ $news->author }}
                                @endif
                            </div>

                            <h2>{{ $news->title }}</h2>

                            <div class="mt-4">
                                {!! html_entity_decode(html_entity_decode($news->content)) !!}
                            </div>

                            <div class="mt-5">
                                <a href="{{ url()->previous() }}" class="theme-btn">
                                    <i class="fas fa-arrow-left-long"></i> Go Back
                                </a>
                            </div>

                        </div>

                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <h4 class="mb-4">Latest News</h4>

                            @foreach($latest as $item)
                            <div class="d-flex mb-3">
                                <img src="{{ asset('storage/'.$item->image) }}"
                                    style="width:90px; height:70px; object-fit:cover; border-radius:6px;" class="me-3">

                                <div>
                                    <a href="{{ route('news.show', $item->id) }}" class="text-dark fw-semibold">
                                        {{ Str::limit($item->title, 45) }}
                                    </a>
                                    <div class="small text-muted mt-1">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ $item->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- News content section end -->

    </main>

    @include('website.partials.footer')

    <a href="#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>

    @include('website.partials.js')

</body>
</html>
