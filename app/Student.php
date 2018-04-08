<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name','email', 'password', 'reg_token', 'api_header','nat_id', 'ac_year', 'university', 'collage', 'dep', 'mobile', 'pic'
    ];

    protected $hidden = ['password'];
}
