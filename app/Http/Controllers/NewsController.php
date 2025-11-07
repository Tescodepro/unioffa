<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->get();
        return view('news.index', compact('news'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'short_title' => 'required',
            'slug' => 'required|unique:news,slug',
            'content' => 'required',
        ]);

        News::create([
            'title' => $request->title,
            'short_title' => $request->short_title,
            'slug' => Str::slug($request->slug),
            'image' => $request->image ?? null,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
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
}
