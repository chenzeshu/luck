<?php

namespace App\Http\Controllers\v1\Rec;

use App\Http\Controllers\Controller;
use App\Http\Repositories\RecRepo;
use App\Models\monthx;
use Illuminate\Http\Request;

class MController extends Controller
{
    protected $repo;
    protected $month = 3;  //默认显示3个月
    function __construct(RecRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 得到X月内的月金X股, 默认方法
     * @param $page
     * @param $size
     */
    public function getData($page, $size)
    {
        $month = $this->month;//默认显示3个月
        $wanTime = date('Y-m-d H:i:s', strtotime("-{$month} month"));
        $wanTime2 = date('Y-m-d H:i:s', time());
        list($data, $pageCount) = $this->dataAffair($page, $size, $wanTime, $wanTime2);

        $curPage = $page;
        return view('rec.m', compact('data','curPage', 'pageCount','month'));
    }

    /**
     * 前端传入时间界限， 筛选出这个时间内的周金X数据
     * @param $page
     * @param $size
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search($page, $size, Request $request)
    {
        list($wanTime, $wanTime2)  = $this->repo->cacheAffair($request, 'diff_m');

        list($data, $pageCount) = $this->dataAffair($page, $size, $wanTime, $wanTime2);

        $curPage = $page;

        return view('rec.m_search', compact('data','curPage', 'pageCount','wanTime'));
    }

    private function dataAffair($page, $size, $wanTime, $wanTime2){
        $begin = ($page-1) * $size;
        $data = monthx::where('date', ">", $wanTime)->where('date', "<", $wanTime2)->offset($begin)->limit($size)->with('stock')->get()->toArray();
        $pageCount = ceil(monthx::where('date', ">", $wanTime)->where('date', "<", $wanTime2)->count()/$size);

        $data = $this->repo->useCarbonTrans($data);

        if($pageCount == 0){
            $pageCount = 1;
        }
        return [$data, $pageCount];
    }


}
