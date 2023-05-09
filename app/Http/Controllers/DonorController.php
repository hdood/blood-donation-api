<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request("query")) {
            return Donor::where(["active" => 1])->where("name", "like", request("query") . "%")->with('donations')->orderBy("created_at", "desc")->paginate(25);
        }
        return Donor::where(["active" => 1])->with('donations')->orderBy("created_at", "desc")->paginate(25);
    }
    public function getInactiveDonors(Request $request)
    {
        if (request("query")) {
            return Donor::where(["active" => 0])->where("name", "like", request("query") . "%")->orderBy("created_at", "desc")->paginate(25);
        }
        return Donor::where(["active" => 0])->orderBy("created_at", "desc")->paginate(25);
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
            "dob" => ['required', 'date'],
            "rhFactor" => ["required", 'in:positive,negative'],
            "bloodGroup" => ["required", "in:a,ab,b,o"]
        ]);
        $data['password'] = Hash::make($data['password']);

        $user = Donor::create($data);
        return response()->json(["user" => $user, "error" => false]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donor $donor)
    {

        $data = $request->validate([
            "name" => ["required"],
            "bloodGroup" => ["required", "in:a,ab,b,o"],
            "phone" => ['required'],
            "address" => ['required'],
            "gender" => ['required'],
            "rhFactor" => ["required", 'in:positive,negative'],
            "dob" => ['required', 'date'],
        ]);

        if ($donor->update([
            'name' => $data['name'],
            "bloodGroup" => $data['bloodGroup'],
            "address" => $data['address'],
            "phone" => $data['phone'],
            "gender" => $data['gender'],
            "dob" => $data['dob'],
            "rhFactor" => $data['rhFactor']
        ])) return response()->json(["error" => false, "donor" => $donor]);

        return response()->json(["error" => "something went wrong"], 500);
    }

    /** 
     * Remove the specified resource from storage.
     */
    public function destroy(Donor $donor)
    {

        $donor->appointments()->delete();
        $donor->delete();

        return response()->json(['error' => false]);
    }

    public function getDonations()
    {

        $donor = Auth::guard('donor')->user();

        return response()->json($donor->donations);
    }

    public function toggleActiveState(Donor $donor)
    {
        $donor->active = !$donor->active;
        if ($donor->save()) {
            return response()->json(['error' => false, 'active' => $donor->active]);
        }
        return response()->json(["error" => "something went wrong"], 500);
    }
}
