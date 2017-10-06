<?php

use Illuminate\Database\Seeder;
use  \App\Models\stock;
use \App\Http\Controllers\v1\StockController;

class DelistSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //用于在stocks表中标注退市股票
        //todo  先插入StockController获取基本数据
        $controller = new StockController();
        $controller->getStock();

        //删除前面285个
        stock::where('id',"<",286)->delete();

        //status 1 退市
        $data = [
            ["code"=>'600001','status'=>'1' ],
            ["code"=>'600002','status'=>'1' ],
            ["code"=>'600003','status'=>'1' ],
            ['code'=>'600349','status'=>'2'],  //这个更牛逼， 2代表未上市
            ["code"=>'600625','status'=>'1' ],
            ["code"=>'600632','status'=>'1' ],
            ["code"=>'600646','status'=>'1' ],
            ["code"=>'600656','status'=>'1' ],
            ["code"=>'600659','status'=>'1' ],
            ["code"=>'601108','status'=>'1' ],
        ];
        foreach ($data as $v){
            stock::where('code', $v['code'])->update([
               'status' => $v['status']
            ]);
        }

    }
}
