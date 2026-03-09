<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController
{
    public function home()
    {
        return view('home');
    }

    public function signIn()
    {
        return view('signIn');
    }
}
