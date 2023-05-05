<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{

    public $fillable = [
        "donor_id",
        "amount",
        "location",
        "type",
        "date"
    ];

    function donor()
    {
        return $this->belongsTo(Donor::class, "donor_id");
    }
    use HasFactory;
}
