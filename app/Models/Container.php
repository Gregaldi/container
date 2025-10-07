<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    //
    protected $fillable = ['container_number','size','asal','status'];



     public function movements()
    {
        return $this->hasMany(ContainerMovements::class, 'container_id');
    }
}
