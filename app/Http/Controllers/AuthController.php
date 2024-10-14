<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;

class AuthController extends Controller
{
  public function register(Request $request) {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8',
      'role' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors());
    }

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => $request->role,
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(['status' => true, 'data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
  }

  public function login(Request $request) {
    if (!Auth::attempt($request->only('email', 'password'))) {
      return response()->json(['status' => false, 'message' => 'Unathorized'], 401);
    }

    $user = User::where('email', $request->email)->firstOrFail();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(['status' => true, 'data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
  }

  public function logout() {
    auth()->user()->tokens()->delete();

    return response()->json(['status' => true, 'message' => 'Logout successful']);
  }
}
