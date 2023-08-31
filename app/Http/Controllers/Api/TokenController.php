<?php

namespace App\Http\Controllers\Api;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function checkTokenValidity()
{
    try {
        JWTAuth::parseToken()->authenticate();

        return response()->json(['valid' => true]);
    } catch (\Exception $e) {
        return response()->json(['valid' => false]);
    }
}
}
