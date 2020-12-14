<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model{

    public $timestamps = false;
    // protected $table = 'my_users';
    // public $timestamp = false;


    public function empleados()
    {
        return $this->hasMany(Empleado::class,'id');
    }
    
}