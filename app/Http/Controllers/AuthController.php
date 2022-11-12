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

    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if($validator->fails())
        {
            return response()->json($validator->messages(),422);
        }

        $user = User::where('email',$request->email)->first();

        if(!$user)
        {
            return response()->json('user not found!!!',404);
        }

        if(!Hash::check($request->password, $user->password))
        {
            return response()->json('password is incorct!!!',422);
        }

        $token=$user->createToken('myToken')->plainTextToken;

        return response()->json([
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function($token,$key){
            $token->delete();
        });

        return response()->json('Loged out successfully!!!',200);
    }
}
