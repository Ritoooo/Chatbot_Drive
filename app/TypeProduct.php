<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeProduct extends Model
{
    
	protected $table = 'type_products';
    protected $fillable = ['nombre'];
    public $timestamps = false;

    public function products(){
    	return $this->hasMany('App\Product','type_id');
    }
}
