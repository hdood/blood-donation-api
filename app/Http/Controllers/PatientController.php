<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        if (request("query")) {
            return Patient::where("name", "like", request("query") . "%")->orderBy("created_at", "desc")->paginate(25);
        }
        return Patient::orderBy("created_at", "desc")->paginate(25);
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
            "gender" => ['required'],
            "dob" => ['required']
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
    public function update(Request $request, Patient $patient)
    {
        $request->validate(
            [
                "name" => ["required"],
                "phone" => ['required'],
                "address" => ['required'],
                "gender" => ['required'],
                "dob" => ['required']
            ]
        );


        if ($patient->update([
            'name' => $request->name,
            "address" => $request->address,
            "phone" => $request->phone,
            "gender" => $request->gender,
            "dob" => $request->dob,
        ])) return response()->json(["error" => false, "patient" => $patient]);

        return response()->json(["error" => "something went wrong"], 500);
    }

    /** 
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {

        $patient->delete();

        return response()->json(['error' => false]);
    }

    public function toggleActiveState(Patient $patient)
    {
        $patient->active = !$patient->active;
        if ($patient->save()) {
            return response()->json(['error' => false, 'active' => $patient->active]);
        }
        return response()->json(["error" => "something went wrong"], 500);
    }
}
