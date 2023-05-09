<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{

    public $fillable = [
        "patient_id",
        'bloodGroup',
        "rhFactor",
        'description'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    use HasFactory;
}
