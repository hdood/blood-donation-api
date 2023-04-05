<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DB::table("donors")->select(['id', 'name', 'address', 'email', 'phone', 'gender', "rhFactor", "bloodType"])->paginate(10);
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
            "gender" => ['required']
        ]);
        $data['rhFactor'] = (int) $data['rhFactor'];

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
}
