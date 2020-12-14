<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model{

    protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $table = 'my_users';
    // public $timestamp = false;
    public function sector(){
        return $this->belongsTo(Sector::class, 'id_sector');
    }
}