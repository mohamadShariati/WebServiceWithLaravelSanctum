<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required|string',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string',
            'c_password'=>'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>$validator->messages(),
                'status'=>422
            ],422);
        }

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);

        $token=$user->createToken('myApp')->plainTextToken;

        return response()->json([
            'user'=>$user,
            'token'=>$token
        ],200);
    }
}
