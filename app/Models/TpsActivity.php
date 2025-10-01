<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpsActivity extends Model
{
    //
    protected $fillable = ['container_nomor_container', 'masuk', 'keluar', 'foto_masuk_depan','foto_masuk_belakang','foto_masuk_kanan','foto_masuk_kiri',
        'foto_keluar_depan','foto_keluar_belakang','foto_keluar_kanan','foto_keluar_kiri'];

    // public function container()
    // {
    //     return $this->belongsTo(Container::class);
    // }
    public function containers()
        {
            return $this->hasMany(Container::class, 'nomor_container', 'container_nomor_container');
        }
    
}
