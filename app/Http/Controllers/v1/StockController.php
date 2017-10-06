<?php

namespace App\Http\Controllers\v1;

use App\Models\stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use QL\QueryList;

class StockController extends Controller
{
    /**
     * 总职能：爬出所有股票CODE+NAME， 存储， 并分页展示vie
     */

    /**
     * 分页展示股票
     */
    public function index()
    {
        $data = stock::paginate(15);
        return view('stock/index', compact('data'));
    }

    public function search($code)
    {
        $data = stock::where('code', $code)->first()->toArray();
        return response()->json($data);
    }

    public function showChoice($stock_id)
    {
        $data = stock::findOrFail($stock_id);
        session(['stock_id' => $stock_id]);
        return view('stock._show', compact('data'));
    }

    public function getStock()
    {
        $url = "http://quote.eastmoney.com/stock_list.html";
        $content = QueryList::get($url)->find(".quotebody ul li a")->texts();
        $content->pop();
        $save = [];
        foreach ($content as $k=>$v){
            $v = str_replace(")", "", $v);
//            $content[$k] = explode("(", $v);
            $v = explode("(", $v);

            $save[$k] = [
                'code' => $v[1],
                'name' => $v[0]
            ];
        }
        DB::table('stocks')->insert($save);
        return "存储完毕";
    }
}
