<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Google_Client;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('patient')->attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(['user' => Auth::guard('patient')->user()]);
        }

        $patient = Patient::where(['email' => $credentials['email']])->first();
        if ($patient) {
            if (!$patient->active) return response()->json(['error' => 'your account is suspended'], 401);
        }

        return response()->json(['error' => 'invalid credentials'], 401);
    }

    public function logout(Request $request): bool
    {
        Auth::guard('patient')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

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
            "dob" => ['required', 'date']
        ]);

        $data['password'] = Hash::make($data['password']);
        $patient = Patient::create($data);

        return $this->authenticatePatientInstance($patient);
    }
    public function googleRegister(Request $request)
    {
        $data = $request->validate([
            'credential_token' => 'required',
            "phone" => ['required'],
            "gender" =>  ['required'],
            "address" => ['required'],
            "dob" => ['required', 'date']

        ]);
        $credentialToken = $data['credential_token'];

        $payload = $this->verifyGoogleToken($credentialToken);
        $data['email'] = $payload['email'];

        $patient = Patient::where('email', $payload['email'])->first();

        if ($patient) {
            return response()->json(['error' => 'user already exists'], 401);
        }

        $data['password'] = Hash::make(uniqid());
        $data['name'] = $payload['name'];
        $patient = Patient::create($data);

        return $this->authenticatePatientInstance($patient);
    }

    function googleLogin(Request $request)
    {
        $data = $request->validate([
            "credential_token" => "required"
        ]);
        $credentialToken = $data['credential_token'];

        $payload = $this->verifyGoogleToken($credentialToken);

        $patient = Patient::where('email', $payload['email'])->first();

        if (!$patient) {
            return response()->json(['error' => 'invalid credentials'], 401);
        }

        return $this->authenticatePatientInstance($patient);
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
        $patient = Patient::where('email', $email)->first();
        if ($patient) {
            return response()->json(['error' => 'invalid credentials'], 401);
        }

        return response()->json(status: 200);
    }

    function authenticatePatientInstance($patient)
    {
        Auth::guard('patient')->login($patient);
        request()->session()->regenerate();
        return response()->json(['user' => Auth::guard('patient')->user()]);
    }
}
