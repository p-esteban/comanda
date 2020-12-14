<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model{

    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'estado_pedidos';
    // public $timestamp = false;

    
}