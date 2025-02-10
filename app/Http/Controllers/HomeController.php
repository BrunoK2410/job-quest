<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Job;

class HomeController extends Controller
{
    // @desc Show home index view
    // @route GET /
    public function index(): View {
        $jobs=Job::latest()->limit(3)->get();
        return view('pages.index',compact('jobs'));
    }
}
