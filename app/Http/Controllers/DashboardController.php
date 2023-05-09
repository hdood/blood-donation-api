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
        $aPositive = Donation::whereHas('donor', function ($query) {
            $query->where(['bloodGroup' =>  'a', 'rhFactor' => 'positive']);
        })->count();
        $aNegative = Donation::whereHas('donor', function ($query) {
            $query->where(['bloodGroup' =>  'a', 'rhFactor' => 'negative']);
        })->count();
        $bPositive = Donation::whereHas('donor', function ($query) {
            $query->where(['bloodGroup' =>  'b', 'rhFactor' => 'positive']);
        })->count();
        $bNegative = Donation::whereHas('donor', function ($query) {
            $query->where(['bloodGroup' =>  'b', 'rhFactor' => 'negative']);
        })->count();
        $abPositive = Donation::whereHas('donor', function ($query) {
            $query->where(['bloodGroup' =>  'ab', 'rhFactor' => 'positive']);
        })->count();
        $abNegative = Donation::whereHas('donor', function ($query) {
            $query->where(['bloodGroup' =>  'ab', 'rhFactor' => 'negative']);
        })->count();
        $oPositive = Donation::whereHas('donor', function ($query) {
            $query->where(['bloodGroup' =>  'o', 'rhFactor' => 'positive']);
        })->count();
        $oNegative = Donation::whereHas('donor', function ($query) {
            $query->where(['bloodGroup' =>  'o', 'rhFactor' => 'negative']);
        })->count();

        return response()->json([
            "donorsCount" => $donorsCount,
            "patientsCount" => $patientsCount,
            "donationsCount" => $donationsCount,
            "bloodBank" => [
                "a" => ['positive' => $aPositive, 'negative' =>  $aNegative],
                "b" => ['positive' => $bPositive, 'negative' =>  $bNegative],
                "ab" => ['positive' => $abPositive, 'negative' =>  $abNegative],
                "o" => ['positive' => $oPositive, 'negative' =>  $oNegative],
            ]
        ]);
    }
}
