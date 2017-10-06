<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class dayx extends Model
{
    protected $guarded = [];

    public function stock()
    {
        return $this->belongsTo(stock::class);
    }
}
