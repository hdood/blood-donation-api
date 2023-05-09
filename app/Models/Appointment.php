<?php

namespace App\Models;

use App\States\AppointmentState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

class Appointment extends Model
{
    public $fillable = [
        'donor_id',
        'date',
        'time',
    ];
    protected $casts = [
        'state' => AppointmentState::class,
    ];

    function donor()
    {
        return $this->belongsTo(Donor::class, "donor_id");
    }


    function questions()
    {
        return $this->belongsToMany(Question::class, 'answers')->as('answer')->withPivot('data')->withTimestamps()->using(Answer::class);
    }
    use HasFactory, HasStates;
}
