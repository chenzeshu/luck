<?php

namespace App\Http\Controllers\v1\Rec;

use App\Http\Repositories\RecRepo;
use App\Models\monthx;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
//5875 3297 225
//用于一些复合数据筛选
class RecController extends Controller
{
    protected $repo;
    protected $month = 3;  //默认显示3个月

    function __construct(RecRepo $repo)
    {
        $this->repo = $repo;
    }
    //月金叉出现同时， 满足存在最后周叉为金叉的股
    //即月表里的股票， 去查周表的股票
    /**
     * @param $page
     * @param $wanTime 月起始日期 $wanTime2 月截止日期 $wanTime3 周起始日期
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getData($page, $size)
    {
        $month = $this->month;//默认显示3个月
        $wanTime = $wanTime3 = date('Y-m-d H:i:s', strtotime("-{$month} month"));
        $wanTime2 = date('Y-m-d H:i:s', time());

        list($data, $pageCount) = $this->dataAffair($page, $size, $wanTime, $wanTime2, $wanTime3);
        $curPage = $page;

        return view('rec.mul', compact('data', 'curPage', 'pageCount','month'));
    }

    /**
     * 前端传入时间界限，获得时间界限内处理过的数据
     * @param $page
     * @param $size
     * @param $diff 默认区间为[-0.1, 0.1]
     * @type $diff的筛选类型, 1周2月,  默认为2
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search($page, $size,  Request $request)
    {
        list($wanTime, $wanTime2, $wanTime3) = $this->repo->cacheAffair($request, 'diff_mul');

        list($data, $pageCount) = $this->dataAffair($page, $size, $wanTime, $wanTime2, $wanTime3, $request->diff1, $request->diff2, $request->type);

        $curPage = $page;

        return view('rec.mul_search', compact('data','curPage', 'pageCount','wanTime'));
    }

    /**
     * 处理$data并获得$pageCount， 同时返回的数组配合list()方法
     * @param $page  //当前页码
     * @param $size  //当前页显示的数据数目
     * @param $wanTime //起始日期
     * @param $wanTime2 //截止日期
     * @param $wanTime3 //周X起始日期
     * @return array
     */
    private function dataAffair($page, $size, $wanTime, $wanTime2, $wanTime3, $diff1=-0.15, $diff2=0.05,  $type = 1)
    {
        $begin = ($page-1) * $size;
        $data = $this->checkCache($wanTime, $wanTime2, $wanTime3);
        //todo 进行diff筛选
        $data = $this->useDiffSieve($diff1, $diff2, $type);
        if(empty($data)){  //todo 若没有符合的diff数据直接返回
            return [[], 1];
        }

        $pageCount = ceil(count($data)/$size);  //页数

        $data = array_slice($data, $begin, $size);  //todo 分页裁剪

        $data = $this->repo->useCarbonTrans2Dimention($data); //todo 转换时间表达方式
        if($pageCount == 0){
            $pageCount = 1;
        }

        return [$data, $pageCount];
    }

    /**
     * 检查是否有缓存，无/过期则通过传入的时间界限$wanTime使用数据库获得数据库
     * @param $wanTime 搜索的起始时间
     * @param $wanTime2 搜索的截止时间
     * @return array
     */
    private function checkCache($wanTime, $wanTime2, $wanTime3){
        //todo 后2个时间也加入校验， 保证了数据是最新数据
        if(getC('multiX')=="" || getC('wanTime') !== $wanTime || getC('wanTime2') !== $wanTime2 || getC('wanTime3') !== $wanTime3){
            $data = monthx::where('date',">", $wanTime)
                          ->where('date',"<", $wanTime2)
                          ->with('weekxes','stock')
                          ->get()
                          ->reject(function($m) use ($wanTime3){
                              $length = count($m['weekxes']);
                              return count($m['weekxes']) === 0 || $m['weekxes'][$length-1]['date'] < $wanTime3;  //剔除掉比$wanTime3早的
                          })
                          ->toArray();

            Cache::put('multiX',$data, 3600);

        }else {
            $data = Cache::get('multiX');
        }
        return $data;
    }

    /**
     * 获得[$diff1, $diff2]区间内的值
     *
     * 默认是使用周筛选
     */
    public function useDiffSieve($diff1, $diff2 , $type)
    {
        $data = getC('multiX');

        $data = collect($data);
        switch ($type){
            case 1: //最近的一个周X
                $sieve = $data->reject(function ($v) use ($diff1, $diff2){
//                                    $value = abs($v['weekxes'][count($v['weekxes'])-1]['diff']);
                                    $value = $v['weekxes'][count($v['weekxes'])-1]['diff'];
                                    return  $value > $diff2 || $value < $diff1;
                                })
                              ->toArray();
                $this->getOut($sieve);
                return $sieve;
                break;
            case 2: //月
                $sieve = $data->reject(function ($v) use ($diff1, $diff2){
//                                    $value = abs($v['diff']);
                                    $value = $v['diff'];
                                    return $value >$diff2 || $value < $diff1;
                              })->toArray();
                $this->getOut($sieve);
                return $sieve;
                break;
            default:
                break;
        }
    }
    /**
     * 组合code成字符串打印, 并缓存
     * @param $data
     * @return string
     */
    private function getOut($data){
        $out = '';
        foreach ($data as $k => $v){
            $out .= $v['stock']['code']." ";
        }
        $out = rtrim($out);
        Cache::put('out', $out, 3600);
        return $out;
    }




}
