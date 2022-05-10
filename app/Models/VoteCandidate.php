<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteCandidate extends Model
{
    use HasFactory;

    protected $table = 'vote_candidate';

    public $timestamps = true;
    protected $dateFormat = 'U';

    protected $casts = [
        'id' => 'integer',
        'void_it' => 'integer',
        'title' => 'string',
    ];
}
