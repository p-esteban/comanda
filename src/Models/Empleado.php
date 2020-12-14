<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model{

    protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $table = 'my_users';
    // public $timestamp = false;

   
    public function sector()
    {
        return $this->belongsTo('App\Model\Sector');
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class,'id_puesto' );
    }
    public function estado()
    {
        return $this->hasOne('App\Model\EstadoEmpleado');
    }
    
}