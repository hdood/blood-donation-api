<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Notifications\BloodRequested;
use App\Notifications\RequestAccepted;
use App\Notifications\RequestRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class BloodRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAcceptedRequests()
    {
        return BloodRequest::where(["accepted" => 1])->with('patient')->get();
    }

    public function getPendingRequests()
    {
        return BloodRequest::where(["accepted" => 0])->with('patient')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "patient_id" => 'required',
            'bloodGroup' => 'required',
            'rhFactor' => 'required',
            'description' => 'required'
        ]);
        $bloodRequest = BloodRequest::create($data);
        if (!$bloodRequest) {
            return response()->json(["error" => "something went wrong"], 500);
        }
        $patient = $bloodRequest->patient;
        $donors = Donor::where(["active" => 1, 'bloodGroup' => $data['bloodGroup'], "rhFactor" => $data["rhFactor"]])->get();
        Notification::send($donors, new BloodRequested($patient, $bloodRequest));
        return response()->json(["error" => false]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BloodRequest $bloodRequest)
    {
        $data = $request->validate([
            'bloodGroup' => 'required',
            'rhFactor' => 'required'
        ]);
        $updated = $bloodRequest->update($data);

        if (!$updated) {
            return response()->json(["error" => 'something went wrong'], 500);
        }
        return response()->json(["error" => false]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $request = BloodRequest::find($id);
        $deleted = $request->delete();
        if (!$deleted) {
            return response()->json(["error" => "something went wrong", "request" => $request], 500);
        }

        return response()->json(["error" => "hello world"]);
    }

    function accept($id)
    {
        $request = BloodRequest::find($id);
        $patient = $request->patient;
        $request->accepted = 1;
        $updated = $request->update();
        if (!$updated) return response()->json(["error" => "something went wrong"], 500);
        $patient->notify(new RequestAccepted);
        return response()->json(["error" => false]);
    }
    function reject($id)
    {
        $request = BloodRequest::find($id);
        $patient = $request->patient;
        $deleted = $request->delete();
        if (!$deleted) {
            return response()->json(["error" => "something went wrong", "request" => $request], 500);
        }
        $patient->notify(new RequestRejected);
        return response()->json(["error" => "hello world"]);
    }
}
