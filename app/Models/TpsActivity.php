<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpsActivity extends Model
{
    //
    protected $fillable = ['container_id', 'masuk', 'keluar', 'foto_masuk', 'foto_keluar'];

    public function container()
    {
        return $this->belongsTo(Container::class);
    }
    
}
