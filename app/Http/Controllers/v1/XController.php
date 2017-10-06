<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\X\MonthController;
use App\Http\Controllers\v1\X\WeekController;
use App\Jobs\SaveCurDayGoldX;
use App\Jobs\SaveCurDayGoldX2;
use App\Jobs\SaveCurMonGoldX;
use App\Jobs\SaveCurWeekGoldX;
use App\Models\stock;
use Illuminate\Support\Facades\DB;

//用途：遍历获得所有股的金叉信息
//PS: 筛选放另外一个控制器做
class XController extends Controller
{
    protected $repo;

    public function __construct()
    {

    }

    public function day()
    {
        SaveCurDayGoldX::dispatch();
        echo "haha";
    }

    public function week(WeekController $weekController)
    {
        SaveCurWeekGoldX::dispatch();

        echo "week finished";
    }

    public function month(MonthController $monthController)
    {
        SaveCurMonGoldX::dispatch();

        echo "month finished";
    }
}
