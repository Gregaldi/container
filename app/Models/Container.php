<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $fillable = ['nomor_container', 'size', 'asal', 'no_plat', 'no_seal'];

    public function terminalActivities()
    {
        return $this->hasMany(TerminalActivity::class);
    }

    public function tpsActivities()
    {
        return $this->hasMany(TpsActivity::class);
    }
}
