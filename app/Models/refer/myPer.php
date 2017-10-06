<?php

namespace App\Models\refer;

use App\Models\stock;
use Illuminate\Database\Eloquent\Model;

class myPer extends Model
{
    protected $guarded = [];

    public function stock()
    {
        return $this->belongsTo(stock::class);
    }
}
