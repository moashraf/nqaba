<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name','email', 'password', 'emp_id', 'reg_token', 'api_header', 'job', 'nat_id', 'mobile', 'pic'
    ];

    protected $hidden = ['password'];
}
