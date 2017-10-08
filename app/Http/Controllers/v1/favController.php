<?php

namespace App\Http\Controllers\v1;

use App\Models\favorite;
use App\Models\stock;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//我的收藏
class favController extends Controller
{
    public function getData($page, $size)
    {
        $start = ($page - 1) * $size;
        $data = favorite::offset($start)->limit($size)->with('stock','monthxes','weekxes')->with('myValues','myTimes')->get()->toArray();

        foreach ($data as $k => $stock){
            if(count($stock['weekxes']) !=0){
                //todo 1. 改日期； 2，算百分比
                $data[$k]['weekxes'][0]['date'] = Carbon::createFromFormat('Y-m-d H:i:s', $stock['weekxes'][0]['date'])->diffForHumans();
                $data[$k]['weekxes'][0]['macd'] =  number_format(abs( $stock['weekxes'][0]['macd'] / $stock['stock']['macd_max']), 4) * 100 . "%";
            }
            if(count($stock['monthxes']) !=0){
                $data[$k]['monthxes'][0]['date']  = Carbon::createFromFormat('Y-m-d H:i:s', $stock['monthxes'][0]['date'])->diffForHumans();
                $data[$k]['monthxes'][0]['macd'] =  number_format(abs( $stock['monthxes'][0]['macd'] / $stock['stock']['macd_max']),4) * 100 . "%";
            }
        }

        return view('fav.index', compact('data'));
    }

    public function save(Request $request)
    {
        $id = $request->id;
        $reason = $request->reason;

        stock::findOrFail($id)->update([
           'fav' => 1
        ]);

        stock::findOrFail($id)->favorites()->updateOrCreate([
            'stock_id'=>$id,
            'reason'=>$reason
        ]);

        return [
            'errno' => 0,
            'msg' => "收藏成功"
        ];
    }

    public function delete($id)
    {
        $re1 = favorite::findOrFail($id)->stock()->update([
           'fav'=>0,
        ]);
        $re2 = favorite::findOrFail($id)->delete();
        if($re1 && $re2){
            return [
                'errno' =>0,
                'msg' => "取消成功！"
            ];
        }
        return [
            'errno' =>1,
            'msg' => "取消失败， 请联系开发者！"
        ];

    }
}
