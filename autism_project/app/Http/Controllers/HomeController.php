<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        if(Auth::check()){
            $user=Auth::user();

            if($user->isAdmin()){
                return redirect()->route('admin.dashboard');
            }

            if($user->isSpecialist()){
                return redirect()->route('specialist.dashboard');
            }

            if($user->isParent()){
                return redirect()->route('parent.dashboard');
            }
        }

            return view('home.index');
        }
    }

