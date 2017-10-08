<?php

namespace App\Http\Controllers\v1\Rec;

use App\Http\Controllers\Controller;
use App\Http\Repositories\RecRepo;
use App\Models\weekx;
use Illuminate\Http\Request;

class WController extends Controller
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

        return view('rec.w', compact('data','curPage', 'pageCount','month'));
    }

    /**
     *
     * @param $page
     * @param $size
     * @param $month
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search($page, $size, Request $request)
    {
        list($wanTime, $wanTime2)  = $this->repo->cacheAffair($request, 'diff_w');

        list($data, $pageCount) = $this->dataAffair($page, $size, $wanTime, $wanTime2);

        $curPage = $page;

        return view('rec.w_search', compact('data','curPage', 'pageCount','wanTime'));
    }

    private function dataAffair($page, $size, $wanTime, $wanTime2){
        $begin = ($page-1) * $size;
        $data = weekx::where('date', ">", $wanTime)->where('date', "<", $wanTime2)->offset($begin)->limit($size)->with('stock')->get()->toArray();
        $pageCount = ceil(weekx::where('date', ">", $wanTime)->where('date', "<", $wanTime2)->count()/$size);

        $data = $this->repo->useCarbonTrans($data);

        if($pageCount == 0){
            $pageCount = 1;
        }

        return [$data, $pageCount];
    }

}
