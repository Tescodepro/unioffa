<?php

namespace App\Http\Controllers\Staff\Pro;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Contracts\View\View;

class ProController extends Controller
{
    public function dashboard(): View
    {
        $totalNews = News::count();
        $activeNews = News::where('is_active', true)->count();
        $inactiveNews = News::where('is_active', false)->count();
        $recentNews = News::latest()->take(5)->get();

        return view('staff.pro.dashboard', compact(
            'totalNews',
            'activeNews',
            'inactiveNews',
            'recentNews'
        ));
    }
}
