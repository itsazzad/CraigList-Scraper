<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    
    protected $table = "proxys";

    protected $fillable = ['ip','port'];
}
