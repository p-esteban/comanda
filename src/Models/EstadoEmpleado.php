<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoEmpleado extends Model{

    protected $primaryKey = 'id';
    public $timestamps = false;
     protected $table = 'estado_empleado';
    // public $timestamp = false;


    // public function sector()
    // {
    //     return $this->hasOne('App\Model\Empleado');
    // }
    
}