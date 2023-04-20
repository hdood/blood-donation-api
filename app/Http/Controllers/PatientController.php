<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        if (request("query")) {
            return Patient::where("name", "like", request("query") . "%")->orderBy("created_at", "desc")->paginate(10);
        }
        return Patient::orderBy("created_at", "desc")->paginate(10);
    }

    /** 
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "min:8"],
            "name" => ["required"],
            "phone" => ['required'],
            "address" => ['required'],
            "gender" => ['required']
        ]);

        Patient::create($data);

        return response()->json(["error" => false]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $Patient)
    {
        $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "min:8"],
            "name" => ["required"],
            "phone" => ['required'],
            "address" => ['required']
        ]);


        $Patient->update([
            'name' => $request->name,
            'email' => $request->email,
            "bloodType" => $request->bloodType,
            "address" => $request->address,
            "phone" => $request->phone
        ]);


        return response()->json(["error" => false]);
    }

    /** 
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return response()->json(['error' => false]);
    }
}
