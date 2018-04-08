<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{

    protected $fillable = [
        'name','email', 'password', 'fcode', 'reg_token', 'api_header', 'job', 'dep', 'nat_id', 'mobile', 'gender', 'gov', 'branch', 'elec_branch', 'graduate', 'pic', 'members_Type'
    ];

    protected $hidden = ['password'];

    public function payments(){
        return $this->hasMany('App\Payment','fcode','fcode');
    }
}
