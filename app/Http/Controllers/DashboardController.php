<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Patient;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getInfo()
    {
        $donorsCount = Donor::all()->count();
        $patientsCount = Patient::all()->count();
        $donationsCount = Donation::all()->count();

        return response()->json(["donorsCount" => $donorsCount, "patientsCount" => $patientsCount, "donationsCount" => $donationsCount]);
    }
}
