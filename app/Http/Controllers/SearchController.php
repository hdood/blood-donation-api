<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{

  public function search(string $term)
  {
    return response()->json(["term" => $term, "donors" => $this->searchDonor($term), "patients" => $this->searchPatient($term)]);
  }
  public function searchDonor($term)
  {
    return Donor::where("name", 'like', $term . "%")->get();
  }

  public function searchPatient($term)
  {
    return Patient::where("name", 'like', $term . "%")->get();
  }
}
