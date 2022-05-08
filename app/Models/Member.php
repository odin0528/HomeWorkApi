<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'member';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'mobile' => 'string',
        'regtime' => 'string',
        'last_login_time' => 'integer',
        'invitation_code' => 'string',
        'ip' => 'string',
        'email' => 'string',
        'name' => 'string',
        'password' => 'string',
        'sex' => 'integer',
        'is_bind_email' => 'integer',
        'topimgurl' => 'string',
        'nickname' => 'string',
        'type' => 'integer',
        'role_id' => 'string',
        'bid' => 'integer',
        'qq' => 'integer',
        'mysign' => 'string',
        'comment' => 'integer',
        'to_comment' => 'integer',
        'chat_num' => 'integer',
        'share_num' => 'integer',
        'praise' => 'integer',
        'focus' => 'integer',
        'favorites' => 'integer',
        'province_id' => 'integer',
        'city_id' => 'integer',
        'status' => 'integer',
    ];
}
