<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Google_Client;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('donor')->attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'active' => 1])) {
            $request->session()->regenerate();

            return response()->json(['user' => Auth::guard('donor')->user()]);
        }

        $donor = Donor::where(['email' => $credentials['email']])->first();
        if ($donor) {
            if (!$donor->active) return response()->json(['error' => 'your account is suspended'], 401);
        }

        return response()->json(['error' => 'invalid credentials'], 401);
    }

    public function logout(Request $request): bool
    {
        Auth::guard('donor')->logout();

        $request->session()->invalidate();

        Session::migrate(true);

        return true;
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            "name" => ['required'],
            "email" => ['required', 'email'],
            "password" => ['required', "min:8"],
            "phone" => ['required'],
            "gender" =>  ['required'],
            "address" => ['required'],
            "bloodGroup" => ['required', 'in:a,b,ab,o'],
            "rhFactor" => ['required', 'in:positive,negative'],
            "dob" => ['required', 'date']
        ]);

        $data['password'] = Hash::make($data['password']);
        $donor = Donor::create($data);

        return response()->json(["error" => false]);
    }
    public function googleRegister(Request $request)
    {
        $data = $request->validate([
            'credential_token' => 'required',
            "phone" => ['required'],
            "gender" =>  ['required'],
            "address" => ['required'],
            "bloodGroup" => ['required', 'in:a,b,ab,o'],
            "rhFactor" => ['required', 'in:positive,negative'],
            "dob" => ['required', 'date']

        ]);
        $credentialToken = $data['credential_token'];

        $payload = $this->verifyGoogleToken($credentialToken);
        $data['email'] = $payload['email'];

        $donor = Donor::where('email', $payload['email'])->first();

        if ($donor) {
            return response()->json(['error' => 'user already exists'], 401);
        }

        $data['password'] = Hash::make(uniqid());
        $data['name'] = $payload['name'];
        $donor = Donor::create($data);

        return response()->json(["error" => false]);
    }

    function googleLogin(Request $request)
    {
        $data = $request->validate([
            "credential_token" => "required"
        ]);
        $credentialToken = $data['credential_token'];

        $payload = $this->verifyGoogleToken($credentialToken);

        $donor = Donor::where(['email' => $payload['email']])->first();

        if (!$donor) {
            return response()->json(['error' => 'invalid credentials'], 401);
        }
        if (!$donor->active) return response()->json(['error' => 'your account is suspended'], 401);

        return $this->authenticateDonorInstance($donor);
    }

    function verifyGoogleToken($credentialToken)
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $payload = $client->verifyIdToken($credentialToken);
        return $payload;
    }

    function checkIfExistsByEmail(Request $request)
    {
        $data = $request->validate([
            'credential_token' => 'required'
        ]);

        $payload = $this->verifyGoogleToken($data['credential_token']);
        $email = $payload['email'];
        $donor = Donor::where('email', $email)->first();
        if ($donor) {
            return response()->json(['error' => 'invalid credentials'], 401);
        }

        return response()->json(status: 200);
    }

    function authenticateDonorInstance($donor)
    {
        Auth::guard('donor')->login($donor);
        request()->session()->regenerate();
        return response()->json(['user' => Auth::guard('donor')->user()]);
    }
}
