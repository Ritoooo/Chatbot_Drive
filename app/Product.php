<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['type_id','nombre','descripcion','preVen','stock', 'caracteristicas'];

    public function typeProduct(){
    	return $this->belongsTo('App\TypeProduct');
    }
}
