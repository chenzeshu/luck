<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/4
 * Time: 15:15
 */

namespace App\Utils;


class Params
{
    //12-26日移动平均线版本 ： 不关注小型金叉(如东方财富通的DIF与DEA粘连时产生的小金叉)
    const EMA12_a =2/13;
    const EMA12_b = 11/13;
    const EMA26_a = 2/27;
    const EMA26_b = 25/27;

    //东方财富通版本 完全吻合
//    const EMA12_a =2/6;
//    const EMA12_b = 4/6;
//    const EMA26_a = 2/11;
//    const EMA26_b = 9/11;

    const DEA_a = 8/10;
    const DEA_b = 2/10;

    const START="20100505";
}