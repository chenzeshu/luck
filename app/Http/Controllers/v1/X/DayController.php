<?php

namespace App\Http\Controllers\v1\X;

use App\Http\Controllers\Controller;
use App\Http\Repositories\x\DayRepo;
use App\Models\stock;

class DayController extends Controller
{
    protected $repo;
    
    public function __construct(DayRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 得到股票的最后5个日金叉
     * @param $code  股票代码
     * @param $start 开始日期 format-example:20100101
     * @param $end 结束日期
     */
    public function getX($code, $start = null, $end = null)
    {
        $data = $this->repo->getDATA($code, $start, $end)->getX()->getFiveGold();
        if($this->repo->delisted){
            //todo 先将股票标记为退市
            stock::where('code', $code)->update([
                'status' => 1
            ]);
            //todo 再返回空数组避免其他表存储；
            return [];
        }
        else{
            return $data;
        }

    }
}
