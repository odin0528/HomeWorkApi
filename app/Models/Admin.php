<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'admin';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'account' => 'string',
        'password' => 'string',
    ];
}
