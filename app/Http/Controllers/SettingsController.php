<?php

namespace App\Http\Controllers;

use App\SMBR\Rom;

class SettingsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
    }

    public function hash()
    {
        return [
            'rom_hash' => Rom::HASH,
            'rom_size' => Rom::SIZE,
        ];
    }
}
