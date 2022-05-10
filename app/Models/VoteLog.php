<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteLog extends Model
{
    use HasFactory;

    protected $primaryKey = ['vote_id', 'email', 'id'];
    public $incrementing = false;

    public $timestamps = true;
    protected $dateFormat = 'U';

    protected $table = 'vote_log';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'email' => 'string',
        'candidate_id' => 'integer',
        'vote_id' => 'integer',
        'created_at'    => 'timestamp'
    ];
}
