<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Answer extends Pivot
{
    public $casts = [
        'data' => 'array'
    ];
}
