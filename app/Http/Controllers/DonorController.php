<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
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
            return Donor::where("name", "like", request("query") . "%")->orderBy("created_at", "desc")->paginate(10);
        }
        return Donor::with("donations")->orderBy("created_at", "desc")->paginate(10);
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
            "bloodType" => ["required", "in:a,ab,b,o"],
            "phone" => ['required'],
            "address" => ['required'],
            "rhFactor" => ['required'],
            "gender" => ['required'],
            "dob" => ['required', 'date']
        ]);
        $data['rhFactor'] = (int) $data['rhFactor'];
        $data['password'] = Hash::make($data['password']);

        Donor::create($data);

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
    public function update(Request $request, Donor $donor)
    {
        $request->validate([
            "email" => ["required", "email"],
            // "password" => ["required", "min:8"]
            "name" => ["required"],
            "bloodType" => ["required", "in:a,ab,b,o"],
            "phone" => ['required'],
            "address" => ['required']
        ]);


        $donor->update([
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
    public function destroy(Donor $donor)
    {
        $donor->delete();

        return response()->json(['error' => false]);
    }

    public function renderCard(Donor $donor)
    {
        $rhFactor = boolval($donor->rhFactor) ? ' positive' : ' negative';
        $bloodType = strtoupper(($donor->bloodType . $rhFactor));

        $pdf = Pdf::loadView('donorcard', ['name' => $donor->name, "bloodType" => $bloodType]);

        return $pdf->download(str_replace(" ", "_", $donor->name) . "_card" . '.pdf');
    }

    public function getDonations()
    {

        $donor = Auth::guard('donor')->user();

        return response()->json($donor->donations);
    }
}
