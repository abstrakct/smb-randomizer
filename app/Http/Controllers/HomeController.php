<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        return view('index');
    }

    public function about()
    {
        return view('about');
    }

//    public function admin()
    //    {
    //        return view('admin.index');
    //    }
}
