<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class HomeController extends Controller
{
<<<<<<< HEAD
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

=======
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
>>>>>>> 3ddfe504ba6f646a5cf9cc58c79f1cb42de2d953
