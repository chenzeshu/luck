@extends('layout.layout')

@section('content')

    <h2>diff推荐： 默认显示 [-0.12, 0.12]内的diff</h2>
    <div class="stock-search">
        <form action="{{url('v1/diff/getdiff/1/15/')}}" method="post" class="uk-form">
            {{csrf_field()}}
            <select name="type">
                <option value="0" @if($type==0) selected @endif>天</option>
                <option value="1"@if($type==1) selected @endif>周</option>
                <option value="2"@if($type==2) selected @endif>月</option>
            </select>
            +
            <select name="type2">
                <option value="3"@if($type2==3) selected @endif>不选择</option>
                <option value="0"@if($type2==0) selected @endif>日X</option>
                <option value="1"@if($type2==1) selected @endif>周X</option>
                <option value="2"@if($type2==2) selected @endif>月X</option>>
            </select>
            <button type="submit" class="uk-button">搜索</button>
        </form>
    </div>

    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
        <thead>
            <th>股票代码</th>
            <th>股票名称</th>
            <th>最新<b style="color:red">【{{$desc}}】</b>diff值</th>
            <th>操作</th>
        </thead>
        <tbody>
        @if(count($data) > 0)
            @foreach($data as $stock)
                <tr>
                    <td>{{$stock['stock']['code']}}</td>
                    <td>{{$stock['stock']['name']}}</td>
                    <td>@if($type==0)
                            {{$stock['d_diff']}}
                    @elseif($type==1)
                            {{$stock['w_diff']}}
                    @else
                            {{$stock['m_diff']}}
                    @endif</td>
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
            <li><a href="{{url('v1/diff/getdiff')."/".($curPage - 1)."/15/"}}"><i class="uk-icon-angle-double-left"></i></a></li>
        @else
            <li><a href=""><i class="uk-icon-angle-double-left"></i></a></li>
        @endif

        @foreach(range(1, $pageCount) as $num)
            @if($num == $curPage)
                <li class="uk-active"><span>{{$num}}</span></li>
            @elseif(  abs($num - $curPage) < 3 || $num < 3 || $pageCount - $num < 2 )
                <li class="uk-disabled"><a href="{{url('v1/diff/getdiff/')."/".($num)."/15/"}}"><span>{{$num}}</span></a></li>
            @elseif( abs($num - $curPage) > 3 )
                ...
            @elseif(abs($num - $curPage) == 3)
                <li><a href="#"><span>...</span></a></li>
            @else
                <li class="uk-disabled"><a href="{{url('v1/diff/getdiff')."/{$num}/15/"}}"><span>{{$num}}</span></a></li>
            @endif
        @endforeach

        @if($curPage == $pageCount )
            <li><a href=""><i class="uk-icon-angle-double-right"></i></a></li>
        @else
            <li><a href="{{url('v1/diff/getdiff')."/".($curPage + 1)."/15/"}}"><i class="uk-icon-angle-double-right"></i></a></li>
        @endif
    </ul>
@endsection