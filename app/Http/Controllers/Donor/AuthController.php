<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('donor')->attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(['user' => Auth::guard('donor')->user()]);
        }

        return response()->json(['error' => 'invalid credentials']);
    }

    public function logout(Request $request): bool
    {
        Auth::guard('donor')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

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
        $admin = Donor::create($data);


        if (Auth::guard('donor')->attempt(["email" => $data['email'], "password" => $request['password']])) {
            $request->session()->regenerate();
            return response()->json(['user' => Auth::guard('donor')->user()]);
        }

        return response()->json(["error" => true]);
    }
}
