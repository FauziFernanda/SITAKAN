<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        return view('frontend/home');
    }

    public function peraturan()
    {
        return view('frontend/peraturan'); 
    }
    public function jadwal()
    {
        return view('frontend/jadwal'); 
    }
        
     public function login()
    {
        return view('auth/login');
    }
}
