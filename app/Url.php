<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    
    protected $table = "urls";

    protected $fillable = ["name"];

    public function links()
    {
        return $this->hasMany('App\Link');
    }    
}
