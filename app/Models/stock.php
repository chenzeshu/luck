<?php

namespace App\Models;

use App\Models\refer\myPer;
use App\Models\refer\myTime;
use Illuminate\Database\Eloquent\Model;

class stock extends Model
{
    protected $guarded = [];

    public function dayxs()
    {
        return $this->hasMany(dayx::class);
    }

    public function weekxes()
    {
        return $this->hasMany(weekx::class);
    }

    public function monthxes()
    {
        return $this->hasMany(monthx::class);
    }

    public function favorites()
    {
        return $this->hasMany(favorite::class);
    }

    public function myTimes()
    {
        return $this->hasMany(myTime::class);
    }

    public function myPers()
    {
        return $this->hasMany(myPer::class);
    }
}
