<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function adminLogin(ValidateRequest $request)
    {
        if (Auth::attempt($request->all())) {
            $accessToken = Auth::user()->createToken('admin')->plainTextToken;

            return response(['name' => Auth::user()->name, 'token' => $accessToken, 'message' => 'Admin authenticated'], 200);
        } else {
            return response(['error' => 'Authentication failed'], 401);
        }
    }

    public function validateToken() {
        return response(['data' => 'Authentication'], 200);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response(['msg' => 'Logged out successfully'], 200);
    }
}
