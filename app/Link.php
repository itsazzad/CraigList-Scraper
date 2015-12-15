<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = "links";

    protected $fillable = ["url_id","name"];

    public function Lead()
    {
        return $this->hasOne('App\Lead');
    }

    public function url(){

    	$this->belongsTo('App/Url');
    }

}
