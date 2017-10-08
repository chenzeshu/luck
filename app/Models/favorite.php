<?php

namespace App\Models;

use App\Models\refer\myTime;
use App\Models\refer\myValue;
use Illuminate\Database\Eloquent\Model;

class favorite extends Model
{
    protected $guarded = [];

    public function stock()
    {
        return $this->belongsTo(stock::class);
    }

    public function monthxes()
    {
        return $this->hasMany(monthx::class, 'stock_id', 'stock_id');

    }

    public function weekxes()
    {
        return $this->hasMany(weekx::class, 'stock_id', 'stock_id');
    }

    public function myValues()
    {
        return $this->hasMany(myValue::class);
    }

    public function myTimes()
    {
        return $this->hasMany(myTime::class);
    }
}
