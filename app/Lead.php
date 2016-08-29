<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    
    protected $table = "leads";

    protected $fillable = ["url_id", "link", "name", "phone", "email", "title", "mapLocation", "body"];

    public function url(){

    	$this->belongsTo('App/Url');
    }
}
