<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{

    public $fillable = [
        'donor_id',
        'date',
        'time',
    ];

    function donor()
    {
        return $this->belongsTo(Donor::class, "donor_id");
    }


    function questions()
    {
        return $this->belongsToMany(Question::class, 'answers')->as('answer')->withPivot('data')->withTimestamps()->using(Answer::class);
    }
    use HasFactory;
}
