<?php

namespace App\Models;

use App\States\Donation\DonationState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;


class Donation extends Model
{

    public $fillable = [
        "donor_id",
        "amount",
        "location",
        "type",
        "date",
    ];
    function donor()
    {
        return $this->belongsTo(Donor::class, "donor_id");
    }

    public function getStateAttribute($value)
    {
        return strtolower(explode("\\", $value)[count(explode("\\", $value)) - 1]);
    }


    use HasStates, HasFactory;
}
