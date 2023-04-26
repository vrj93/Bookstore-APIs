<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function adminLogin(ValidateRequest $request)
    {
        if (Auth::attempt($request->all())) {
            $accessToken = Auth::user()->createToken('admin')->plainTextToken;

            return response(['name' => Auth::user()->name, 'token' => $accessToken, 'message' => 'Admin authenticated'], 201);
        } else {
            return response(['error' => 'Authentication failed'], 401);
        }
    }
}
