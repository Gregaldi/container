<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminalActivity extends Model
{
    //
    protected $fillable = [
        'container_no_plat',
        'masuk',
        'keluar',
    ];

    public function container()
    {
        return $this->belongsTo(Container::class);
    }
}
