<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\University;
use Illuminate\Http\Request;

class HomeController extends Controller
{
   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        if (auth()->check()) {
        $role = auth()->user()->role;

        if ($role === 'ADMIN') {
            // Render view dashboard admin langsung
            return view('pages.admin.dashboard');
        } elseif ($role === 'KONTRIBUTOR') {
            // Render view dashboard kontributor langsung
            return view('pages.kontributor.dashboard');
        } elseif ($role === 'USER') {
            // Render homepage user
            return view('pages.home');
        }
    }
    $universities = University::all();
        return view('pages.home',compact('universities'));
    }

    public function about()
    {

        return view('pages.about');
    }
}
