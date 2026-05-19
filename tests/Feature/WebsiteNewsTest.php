<?php

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest pages render default open graph meta tags', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('<meta property="og:type" content="website">', false);
    $response->assertSee('<meta property="og:title" content="Home">', false);
    $response->assertSee('<meta property="og:image" content="'.asset('assets/img/logo/logo.jpeg').'">', false);
});

test('news details page renders custom open graph meta tags', function () {
    $news = News::create([
        'title' => 'Important University News Update',
        'short_title' => 'This is a short summary of the news.',
        'slug' => 'important-university-news-update',
        'image' => 'news/sample.jpg',
        'content' => '&lt;p&gt;This is the actual detail content of the news.&lt;/p&gt;',
        'is_active' => true,
    ]);

    $response = $this->get(route('news.show', $news->id));

    $response->assertStatus(200);
    $response->assertSee('<meta property="og:type" content="article">', false);
    $response->assertSee('<meta property="og:title" content="Important University News Update">', false);
    $response->assertSee('<meta property="og:description" content="This is a short summary of the news.">', false);
    $response->assertSee('<meta property="og:image" content="'.asset('storage/news/sample.jpg').'">', false);
    $response->assertSee('<meta property="og:url" content="'.route('news.show', $news->id).'">', false);

    $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
    $response->assertSee('<meta name="twitter:title" content="Important University News Update">', false);
    $response->assertSee('<meta name="twitter:image" content="'.asset('storage/news/sample.jpg').'">', false);
});
