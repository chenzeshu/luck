<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class weekx extends Model
{
    protected $guarded = [];

    public function stock()
    {
        return $this->belongsTo(stock::class);
    }

    public function monthx()
    {
        return $this->belongsTo(monthx::class, 'stock_id', 'stock_id');
    }
}
