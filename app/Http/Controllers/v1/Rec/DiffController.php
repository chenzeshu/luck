<?php

namespace App\Http\Controllers\v1\Rec;

use App\Models\dayx;
use App\Models\Diff;
use App\Models\monthx;
use App\Models\weekx;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class DiffController extends Controller
{
    /**
     * @param $type 类型 0日 1周 2月
     */
    public function getData($page, $pageSize, Request $request)
    {
        $type = $request->type;
        $type2 = $request->type2;
        if (getC('diff_type') == null && getC('diff_type2') ==null){ //第一次左侧导航栏进入->0按天:3不选择叉

            $type = 0;
            $type2 = 3;
            $this->saveType1and2($type, $type2);

        } elseif ( $type == null && $type2 == null) {  //todo 翻页

            return $this->changePage($page, $pageSize);

        } elseif ( $type !== getC('diff_type') || $type2 !== getC('diff_type2')){ //todo 更新

            $this->saveType1and2($type, $type2);

        }

        list($typeWord, $desc) = $this->switchType($type);
                         $list = $this->switchType2($type2);

        Cache::put('desc', $desc, 3600);

        $begin = ($page -1 )* $pageSize;

        //todo 如果选择了x
        if(!empty($list)){
            if($type == getC('diff_type'))
                $data =  Diff::where($typeWord, '<', '0.12')->where($typeWord, '>', '-0.12')
                    ->with('stock')->get()
                    ->reject(function ($item) use ($list){
                        return strpos($list, $item['stock_id']) == false;
                    })->toArray();
            Cache::put('diff_data', $data, 3600);
            $pageCount = count($data)/$pageSize;
            $data = array_slice($data, $begin, $pageSize);
            $curPage = $page;
            return view('rec.diff', compact('data', 'curPage', 'pageCount','type', 'type2', 'desc'));
        }
        //todo 如果没有选择X

        $data = Diff::where($typeWord, '<', '0.12')->where($typeWord, '>', '-0.12')
            ->offset($begin)->limit($pageSize)
            ->with('stock')->get()->toArray();
        Cache::put('diff_data', $data, 3600);
        $curPage = $page;
        $pageCount = ceil(Diff::where($typeWord, '<', '0.12')->where($typeWord, '>', '-0.12')->count()/$pageSize);

        return view('rec.diff', compact('data', 'curPage', 'pageCount','type', 'type2', 'desc'));
    }

    //$type 指第一个diff按0天,1周,2月
    private function switchType($type){
        switch ($type){
            case 0:
                $typeWord = "d_diff";
                $desc = '天';
                break;
            case 1:
                $typeWord = "w_diff";
                $desc = '周';
                break;
            case 2:
                $typeWord = "m_diff";
                $desc = '月';
                break;
            default:
                $typeWord = "d_diff";
                $desc = '天';
                break;
        }
        return [$typeWord, $desc];
    }

    //$type2 指叉 0天 1周 2月 3不选
    private function switchType2($type2){
        switch ($type2){
            case 0:
                $list = dayx::pluck('stock_id')->toArray();
                $list = implode(',', $list);
                break;
            case 1:
                $list = weekx::pluck('stock_id')->toArray();
                $list = implode(',', $list);
                break;
            case 2:
                $list = monthx::pluck('stock_id')->toArray();
                $list = implode(',', $list);
                break;
            default:
                $list = [];
                break;
        }
        return $list;
    }

    private function changePage($page, $pageSize){
        $type = getC('diff_type');
        $type2 = getC('diff_type2');
        $desc = getC('desc');
        $begin = ($page-1) * $pageSize;
        $data = getC('diff_data');
        $pageCount = count($data)/$pageSize;
        $data = array_slice($data, $begin, $pageSize);
        $curPage = $page;
        return view('rec.diff', compact('data', 'curPage', 'pageCount','type', 'type2', 'desc'));
    }

    private function saveType1and2($type, $type2){
        Cache::put('diff_type', $type,3600);
        Cache::put('diff_type2', $type2,3600);
    }
}
