<?php

namespace App\Models\refer;

use App\Models\favorite;
use Illuminate\Database\Eloquent\Model;

class myTime extends Model
{
    protected $guarded = [];

    public function favorite()
    {
        return $this->belongsTo(favorite::class);
    }
}
