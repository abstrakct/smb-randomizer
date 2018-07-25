<?php

namespace SMBR\Http\Controllers;

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

//    public function admin()
//    {
//        return view('admin.index');
//    }
}
