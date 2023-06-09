<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{


  public function login(Request $request): JsonResponse
  {

    $credentials = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
    ]);

    if (Auth::guard('admin')->attempt($credentials)) {
      $request->session()->regenerate();

      return response()->json(['user' => Auth::guard('admin')->user()]);
    }

    return response()->json(['error' => 'invalid credentials']);
  }

  public function logout(Request $request): bool
  {
    Auth::guard('admin')->logout();

    $request->session()->invalidate();

    Session::migrate(true);

    return true;
  }

  public function register(Request $request)
  {
    $data = $request->validate([
      "name" => ['required'],
      "email" => ['required', 'email'],
      "password" => ['required'],
      "email" => ['required'],
      "phone" => ['required'],
      "address" => ['required'],
      "dob" => ['required', 'date']
    ]);

    $data['password'] = Hash::make($data['password']);
    $admin = Admin::create($data);

    if (Auth::guard('admin')->attempt(["email" => $data['email'], "password" => $request['password']])) {
      $request->session()->regenerate();
      return response()->json(['user' => Auth::guard('admin')->user()]);
    }

    return response()->json(["error" => true]);
  }
}
