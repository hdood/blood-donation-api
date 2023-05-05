<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DonationsController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return Donation::all()->paginate(10);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $data = $request->validate([
      "donor_id" =>  "required",
      "amount" => "required",
      "type" => "required",
      "location" => "required"
    ]);
    $data['date'] = Carbon::now();
    $donation = Donation::create($data);
    if ($donation) {
      return response()->json(["error" => false, "donations" => Donation::where(["donor_id" => $data["donor_id"]])->get()]);
    }
    return response()->json(["error" => "something went wrong"], 500);
  }

  /**
   * Display the specified resource.
   */
  public function show(Donation $donation)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Donation $donation)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Donation $donation)
  {
    //
  }
}
