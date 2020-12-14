<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model{

    protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $table = 'my_users';
    // public $timestamp = false;

     public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    public function pedido(){
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
    public function estado(){
        return $this->belongsTo(EstadoPedido::class, 'id_estado');
    }
}