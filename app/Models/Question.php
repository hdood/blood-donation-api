<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{

    protected $casts = [
        'data' => 'array'
    ];


    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'answers')->as('answer')->withPivot('data')->withTimestamps()->using(Answer::class);;
    }
    use HasFactory;
}
