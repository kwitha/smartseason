<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Authcontroller extends Controller
{
    //login and return a sanctum token+user details

    public function login(LoginRequest $request):JsonResponse
    {
        if(!Auth::attempt($request->only('email','password')))
            {
                throw ValidationException::withMessages([
                    'email'=>['The provided credentials are incorrect.'],
                ]);
            }

            $User =Auth::user();
            $token =$User->createToken('api-token')->plainTextToken;
            return response()->json(
                [
                  'token'=> $token,
                   'user'=>[
                    'id'=>$User->id,
                    'name'=>$User->name,
                    'email'=>$User->email,
                    'role'=>$User->role,
                ]
            ]);
    }
    public function logout(Request $request):JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out successfully.']);
    }

    public function me(Request $request):JsonResponse
    {
        $User =$request->User();
        return response()->json([
            'id'=>$User->id,
            'name'=>$User->name,
            'email'=>$User->email,
            'role'=>$User->role,
        ]);
    }
}

