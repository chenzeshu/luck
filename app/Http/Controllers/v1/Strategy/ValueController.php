<?php

namespace App\Http\Controllers\v1\Strategy;

use App\Models\favorite;
use App\Models\refer\myValue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ValueController extends Controller
{
    public function getData($page, $size)
    {
        $begin = ($page - 1) * $size;
        $data = myValue::offset($begin)->limit($size)->with('favorite.stock')->get()->toArray();
        $pageCount = ceil(count(myValue::offset($begin)->limit($size)->with('favorite.stock')->count())/$size);
        $curPage = $page;
        return view('strategy.index', compact('data', 'pageCount', 'curPage'));
    }

    public function create(Request $request)
    {
        $tem_id = config('app.sms.queue_warn'); //先用这个

        favorite::findOrFail($request->id)->myValues()->create([
            'value' => $request->val,
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
        myValue::findOrFail($request->id)->update([
           'value'=>$request->val,
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
        $re = myValue::findOrFail($id)->delete();
        if($re){
            return [
                'errno' => 0,
                'msg' =>'删除成功'
            ];
        }
    }

    public function known($id)
    {
        $re = myValue::findOrFail($id)->update([
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
