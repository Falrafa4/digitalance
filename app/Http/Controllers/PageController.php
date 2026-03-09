<?php

namespace App\Http\Controllers;

class PageController
{
    public function home()
    {
        return view('home');
    }

    public function login()
    {
        return view('login');
    }
}
