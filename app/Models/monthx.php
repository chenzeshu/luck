<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class monthx extends Model
{
    protected $guarded = [];

    public function stock()
    {
        return $this->belongsTo(stock::class);
    }

    public function favorite()
    {
        return $this->belongsTo(favorite::class);
    }

    public function weekxes()
    {
        return $this->hasMany(weekx::class, 'stock_id', 'stock_id');
    }
}
