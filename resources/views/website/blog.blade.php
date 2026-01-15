<!DOCTYPE html>
<html lang="en">

@include('website.partials.head')

<body>

    @include('website.partials.menu')

    <main class="main">

        <!-- breadcrumb -->
        <div class="site-breadcrumb" style="background: url('{{ asset('assets/img/slider/slider-1.jpg') }}')">
            <div class="container">
                <h2 class="breadcrumb-title">News & Blog</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="active">Blog</li>
                </ul>
            </div>
        </div>
        <!-- breadcrumb end -->

        <!-- blog area -->
        <div class="blog-area py-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mx-auto">
                        <div class="site-heading text-center">
                            <span class="site-title-tagline"><i class="far fa-book-open-reader"></i> Our Blog</span>
                            <h2 class="site-title">Latest News & <span>Updates</span></h2>
                            <p>Stay updated with the latest news, insights, and articles from our university community.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @forelse($news as $item)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="blog-item wow fadeInUp" data-wow-delay=".25s">

                                <div class="blog-date">
                                    <i class="fal fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('F d, Y') }}
                                </div>

                                <div class="blog-item-img">
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                                </div>

                                <div class="blog-item-info">
                                    <h4 class="blog-title">
                                        <a href="{{ route('news.show', $item->id) }}">{{ $item->title }}</a>
                                    </h4>
                                    <p>{!! Str::limit(strip_tags(html_entity_decode(html_entity_decode($item->content))), 100) !!}
                                    </p>
                                    <a class="theme-btn" href="{{ route('news.show', $item->id) }}">
                                        Read More <i class="fas fa-arrow-right-long"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="col-lg-12">
                            <div class="text-center py-5">
                                <i class="far fa-newspaper fa-4x text-muted mb-4"></i>
                                <h4>No news available right now.</h4>
                                <p class="text-muted">Check back later for the latest updates from our university.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($news->hasPages())
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                {{ $news->links() }}
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
        <!-- blog area end -->

    </main>

    @include('website.partials.footer')

    <a href="#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>

    @include('website.partials.js')

</body>

</html>