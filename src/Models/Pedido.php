<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model{

    protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $table = 'my_users';
    // public $timestamp = false;

    public function mesa(){
        return $this->belongsTo(Mesa::class, 'id_mesa');
    }
    public function sector(){
        return $this->belongsTo(Sector::class, 'id_sector');
    }

}