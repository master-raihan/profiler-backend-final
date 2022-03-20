<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'user_id','tag_value',
    ];

    protected $hidden = [

    ];
}
