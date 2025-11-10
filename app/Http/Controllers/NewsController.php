<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->get();
        return view('staff.ict.news', compact('news'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:news,title',
            'short_title' => 'required',
            'content' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
        }

        News::create([
            'title'       => $request->title,
            'short_title' => $request->short_title,
            'slug'        => Str::slug($request->slug ?? $request->title),
            'image'       => $imagePath,
            'content'     => $request->content,
            'is_active'   => $request->has('is_active'),
        ]);

        return back()->with('success', 'News added successfully.');
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'short_title' => 'required',
            'slug' => 'required|unique:news,slug,' . $news->id,
            'content' => 'required',
        ]);

        $news->update([
            'title' => $request->title,
            'short_title' => $request->short_title,
            'slug' => Str::slug($request->slug),
            'image' => $request->image ?? $news->image,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'News updated successfully.');
    }
    
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        return back()->with('success', 'News deleted successfully.');
    }

    public function show(News $news)
    {
        $title = $news->title;
        $latest = News::where('id', '!=', $news->id)->latest()->take(5)->get();
        return view('website.news-details', compact('news', 'latest', 'title'));
    }
}
