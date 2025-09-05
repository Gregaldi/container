<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $primaryKey = 'nomor_container';
    public $incrementing = false; // karena bukan auto increment
    protected $keyType = 'string';
    protected $fillable = ['nomor_container', 'size', 'asal', 'no_plat', 'no_seal', 'foto_no_plat','foto_no_seal', 'foto_nomor_container'];

    // public function terminalActivities()
    // {
    //     return $this->hasMany(TerminalActivity::class);
    // }

    // public function tpsActivities()
    // {
    //     return $this->hasMany(TpsActivity::class);
    // }

    public function terminalActivities()
{
    // Assuming the foreign key in terminal_activities is 'nomor_container'
    return $this->hasMany(TerminalActivity::class, 'nomor_container', 'nomor_container');
}

public function tpsActivities()
{
    // Assuming the foreign key in tps_activities is 'nomor_container'
    return $this->hasMany(TpsActivity::class, 'nomor_container', 'nomor_container');
}
}
