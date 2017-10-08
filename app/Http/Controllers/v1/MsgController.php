<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\msg;
use Illuminate\Http\Request;

class MsgController extends Controller
{
    /**
     * 拿到未读消息
     * @param $page
     * @param $size
     */
    public function unread($page, $size)
    {
        $begin = ($page-1) * $size;
        $data = msg::where('read', 0)->offset($begin)->limit($size)->get()->toArray();
        $pageCount = ceil(msg::where('read',0)->count()/$size);
        if($pageCount == 0){
            $pageCount = 1;
        }
        $curPage = $page;
        return view('msg.index', compact('data', 'pageCount', 'curPage'));
    }

    /**
     * 拿到已读消息
     * @param $page
     * @param $size
     */
    public function read($page, $size)
    {
        $begin = ($page-1) * $size;
        $data = msg::where('read', 1)->offset($begin)->limit($size)->get()->toArray();
        $pageCount = ceil(msg::where('read',1)->count()/$size);
        if($pageCount == 0){
            $pageCount = 1;
        }
        $curPage = $page;
        return view('msg.read', compact('data', 'pageCount', 'curPage'));
    }

    /**
     * 从未读变成已读
     */
    public function change($id)
    {
        msg::findOrFail($id)->update([
            'read' => 1
        ]);
        return [
            'msg' => "已读"
        ];
    }


    /**
     * 删除已读信息
     */
    public function delete($id)
    {
        msg::findOrFail($id)->delete();
        return [
            'msg' => "删除成功"
        ];
    }
}
