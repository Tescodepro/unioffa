<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function home(){
        $title = "Home";
        $events = [];
        $blogs = [];
        return view('website.home', compact('title', 'events', 'blogs'));
    }

    public function contact(){
        $title = "Contact Us";
        return view('website.contact', compact('title'));
    }
}
