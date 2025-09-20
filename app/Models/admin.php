<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class admin extends Model
{
    protected $guarded = [];

    public function user(){
        return $this->hasOne(user::class);
    }
}
