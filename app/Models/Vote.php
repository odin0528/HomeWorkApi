<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'vote';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'start_time' => 'integer',
        'end_time' => 'integer',
        'status' => 'integer',
    ];
}
