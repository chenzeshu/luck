<?php

namespace App\Models\refer;

use Illuminate\Database\Eloquent\Model;

class myTime extends Model
{
    protected $guarded = [];

    public function stock()
    {
        return $this->belongsTo(stock::class);
    }
}
