<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $table = 'login_logs';

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
    ];
}
