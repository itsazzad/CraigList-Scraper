<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    
    protected $table = "urls";

    protected $fillable = ["name"];

    public function leads()
    {
        return $this->hasMany('App\Lead');
    }    
}
