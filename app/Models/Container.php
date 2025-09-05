<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $primaryKey = 'nomor_container';
    public $incrementing = false; // karena bukan auto increment
    protected $keyType = 'string';
    protected $fillable = ['nomor_container', 'size', 'asal', 'no_plat', 'no_seal', 'foto_no_plat','foto_no_seal', 'foto_nomor_container'];

    public function terminalActivities()
    {
        return $this->hasMany(TerminalActivity::class);
    }

    public function tpsActivities()
    {
        return $this->hasMany(TpsActivity::class);
    }
}
