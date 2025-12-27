<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
    if(Auth::check()){
    $user=Auth::user();
    if($user->isAdmin()){
        return redirect()->route('admin.dashboard');
    }
       if($user->isParent()){
        return redirect()->route('parentprofile.dashboard');
    }
   if($user->isSpecialist()){
        return redirect()->route('specialist.dashboard');
    }
    }
    return view('home.index');


    }
}
