<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\UserService;

class UserController extends Controller
{
    public function register(Request $request){
        $response = (new UserService($request -> email, $request -> password))->register($request -> devicename);
        return response()-> json($response);
    }

    public function login(Request $request){

    }
}
