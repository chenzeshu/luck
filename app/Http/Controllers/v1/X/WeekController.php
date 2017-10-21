<?php

namespace App\Http\Controllers\v1\X;

use App\Http\Repositories\x\WeekRepo;
use App\Models\stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WeekController extends Controller
{
    protected $repo;

    public function __construct(WeekRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 得到股票的周金叉信息
     * @param $code  股票代码
     * @param $start 开始日期 format-example:20100101
     * @param $end 结束日期
     */
    public function getX($code, $start, $end)
    {
        $data = $this->repo->getDATA($code , $start, $end)->getX()->getLastGold();
        dd($data);
        if($this->repo->delisted){
            //todo 先将股票标记为退市
            stock::where('code', $code)->update([
                'status' => 1
            ]);
            //todo 再返回空数组避免其他表存储；
            return [];
        }
        elseif($this->repo->new == true){//新股（不满2周）
            stock::where('code', $code)->update([
                'status'=> 3
            ]);
        }
        else{
            return $data;
        }
    }
}
