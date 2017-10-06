<?php

function selectMax($arr){
    $max = $arr[0];
    $length = count($arr);
    for($i = 0 ; $i< ($length - 1); $i++){
        if($arr[$i] < $arr[$i+1]){
            $max = $arr[$i+1];
        }
    }
    return $max;
}