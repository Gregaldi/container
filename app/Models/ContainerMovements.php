<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerMovements extends Model
{
    //
    protected $fillable = ['container_id','direction','truck_plate','truck_plate_out','seal_ship','seal_tps','photos','photo_out','notes','timestamp'];


    protected $casts = [
    'photos' => 'array',
    'timestamp' => 'datetime'
    ];


    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }
}
