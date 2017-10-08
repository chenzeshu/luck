@extends('layout.layout')

@section('content')
    <h2>复合周月金叉推荐： 默认显示 <b style="color:red">{{getC('diff_mul')}}- {{getC('diff_mul2')}}个月</b>内出现月金叉且周也为金叉的股票</h2>
    <div class="stock-search" onkeydown="mytest()">
        <form action="{{url('v1/rec/mulsearch/1/15')}}" method="post" class="uk-form">
            {{csrf_field()}}
            <input type="" name="wanTime" data-uk-datepicker="{format:'YYYY-MM-DD'}" placeholder="选择起始日期" class="m-date-picker">
            <input type="" name="wanTime2" data-uk-datepicker="{format:'YYYY-MM-DD'}" placeholder="选择截止日期" class="m-date-picker">
            <button type="submit" class="uk-button uk-button-primary">搜索</button>
            <button onclick="returnToList()" class="uk-button">回到默认列表</button>
        </form>
    </div>

    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
        <thead>
        <th>股票代码</th>
        <th>股票名称</th>
        <th><span style="color:red">月金叉</span>最后出现时间</th>
        <th><span style="color:red">周金叉</span>最后出现时间</th>
        <th>操作</th>
        </thead>
        <tbody>
        @if(count($data) > 0)
            @foreach($data as $stock)
                <tr>
                    <td>{{$stock['stock']['code']}}</td>
                    <td>{{$stock['stock']['name']}}</td>
                    <td>{{$stock['date']}}</td>
                    <td>{{$stock['weekxes'][0]['date']}}</td>
                    <td>
                        @include("rec._saveFav")
                        <button class="uk-button" onclick="toDong('{{$stock['stock']['code']}}')">东方财富通</button>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    <ul class="uk-pagination">
        @if($curPage > 1)
            <li><a href="{{url('v1/rec/mulsearch')."/".($curPage - 1)."/15"}}"><i class="uk-icon-angle-double-left"></i></a></li>
        @else
            <li><a href=""><i class="uk-icon-angle-double-left"></i></a></li>
        @endif

        @foreach(range(1, $pageCount) as $num)
            @if($num == $curPage)
                <li class="uk-active"><span>{{$num}}</span></li>
            @elseif(  abs($num - $curPage) < 3 || $num < 3 || $pageCount - $num < 2 )
                <li class="uk-disabled"><a href="{{url('v1/rec/mulsearch/')."/".($num)."/15"}}"><span>{{$num}}</span></a></li>
            @elseif( abs($num - $curPage) > 3 )
                ...
            @elseif(abs($num - $curPage) == 3)
                <li><a href="#"><span>...</span></a></li>
            @else
                <li class="uk-disabled"><a href="{{url('v1/rec/mulsearch')."/{$num}/15/"}}"><span>{{$num}}</span></a></li>
            @endif
        @endforeach

        @if($curPage == $pageCount )
            <li><a href=""><i class="uk-icon-angle-double-right"></i></a></li>
        @else
            <li><a href="{{url('v1/rec/mulsearch')."/".($curPage + 1)."/15"}}"><i class="uk-icon-angle-double-right"></i></a></li>
        @endif
    </ul>
@endsection