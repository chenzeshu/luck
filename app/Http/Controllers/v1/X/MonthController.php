<?php

namespace App\Http\Controllers\v1\X;

use App\Http\Repositories\x\MonthRepo;
use App\Models\stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MonthController extends Controller
{
    protected $repo;
    function __construct(MonthRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 得到股票的月金叉数据
     * @param $code  股票代码
     * @param $start 开始日期 format-example:20100101
     * @param $end 结束日期
     */
    public function getX($code, $start = null, $end = null)
    {
        $data = $this->repo->getDATA($code, $start, $end)->getX()->checkLastOne()->getLastGold();
        if($this->repo->delisted){
            //todo 先将股票标记为退市
            stock::where('code', $code)->update([
                'status' => 1
            ]);
            //todo 再返回空数组避免其他表存储；
            return [];
        }
        elseif($this->repo->new == true){//新股 不满2月
            stock::where('code', $code)->update([
               'status'=> 2
            ]);
        }
        else{
            return $data;
        }
    }
}
