<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = "links";

    protected $fillable = ["url_id","name"];

    public function lead()
    {
        return $this->hasOne('App\Lead');
    }

    public function url(){

    	return $this->belongsTo('App\Url');
    }

}
