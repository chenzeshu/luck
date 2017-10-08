<?php

namespace App\Http\Controllers\v1\Strategy;

use App\Models\favorite;
use App\Models\refer\myTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimeController extends Controller
{
    public function getData($page, $size)
    {
        $begin = ($page - 1) * $size;
        $data = myTime::offset($begin)->limit($size)->with('favorite.stock')->get()->toArray();
        $pageCount = ceil(count(myTime::offset($begin)->limit($size)->with('favorite.stock')->count())/$size);
        $curPage = $page;
        return view('strategy.indexTime', compact('data', 'pageCount', 'curPage'));
    }

    public function create(Request $request)
    {
        $tem_id = config('app.sms.queue_warn'); //先用这个

        favorite::findOrFail($request->id)->myTimes()->updateOrCreate([
            'favorite_id'=>$request->id,
            'refertime' => $request->val,
            'msg' => $request->msg,
            'tem_id' => $tem_id
        ]);

        return [
            'errno' => 0,
            'msg' =>'存储成功'
        ];
    }

    public function update(Request $request)
    {
        myTime::findOrFail($request->id)->update([
            'refertime'=>$request->val,
            'msg' => $request->msg,
            'known' => 0  //确保每次修改可以重新激活通知
        ]);

        return [
            'errno' => 0,
            'msg' =>'修改成功'
        ];
    }

    public function delete($id)
    {
        $re = myTime::findOrFail($id)->delete();
        if($re){
            return [
                'errno' => 0,
                'msg' =>'删除成功'
            ];
        }
    }

    public function known($id)
    {
        $re = myTime::findOrFail($id)->update([
            'known' => 1
        ]);
        if($re){
            return [
                'errno' => 0,
                'msg' =>'不再通知'
            ];
        }
    }
}
