<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model{

    protected $primaryKey = 'id';
    public $timestamps = false;
     protected $table = 'sectores';
    // public $timestamp = false;


    // public function empleado()
    // {
    //     return $this->hasMany('App\Model\Empleado');
    // }
    
}