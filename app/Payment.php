<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'fcode', 'range', 'total', 'payment_code', 'confirmed', 'notes'
    ];

    public function member(){
        return $this->belongsTo('App\Payment','fcode','fcode');
    }
}
