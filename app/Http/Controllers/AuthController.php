<?php

namespace App\Http\Controllers;

use Auth;
use View;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */

     public function getLogin(){
       return view('auth.login');
     }

     public function postLogin(Request $request){
       $name = $request->input('name');
       $password = $request->input('password');
       if (Auth::attempt(['name' => $name, 'password' => $password])) {
           // Authentication passed...
           return View::make('auth.login')->with('loginerrors','yaaay!!! correcto');
       }
       else{
         return View::make('auth.login')->with('loginerrors','Credenciales no son correcto');
       }
     }

}
