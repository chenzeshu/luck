<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\MACD\MACDRepo;
use App\Models\stock;
use Illuminate\Support\Facades\DB;

class MACDController extends Controller
{
    protected $repo;

    function __construct(MACDRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 得到所有股票的列表并存入stock表
     * @return string
     */
    public function getStockList() {
        if(stock::count() >0 ){
            return '已有数据, 如果需要更新, 请更改方法';
        }else {
            DB::table('stocks')->truncate();

            $save = $this->repo->getMain();
            $save2 = $this->repo->getOthersFromTu();

            $save = array_merge($save, $save2);
            DB::table('stocks')->insert($save);

            return "存储完毕";
        }
    }


    /**
     * 1. 从网易接口获得一只股票的数据
     * 2. 进行处理
     */
    public function dragDataFromWY($code, $start, $now)
    {
        return $this->repo->init($code, $start, $now)->calDayMACD()->getX();
    }


}
