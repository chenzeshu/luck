@extends('layout.layout')

@section('content')
<div class="stock-search" onkeydown="mytest()">
    <input type="text" placeholder="填写股票代码" value="" id="stock-search">
    <button onclick="searchStock()" class="uk-button">搜索</button>
    <button onclick="returnToList()" class="uk-button">回到总列表</button>
</div>

<style>
    #stock-search{
        display: inline-block;
        height: 25px;
        width: 15vw;
        border-bottom: 1px dotted rgba(0,0,0,.2);
        background :#f5f7f9;
        font-size :16px;
        line-height: 24px;
        text-indent: 16px;
        outline: 0;
    }
    .save-fav{
        display: inline-block;
        height: 100px;
        width: 25vw;
        margin-bottom : 15px;
        padding-top:15px;
        border-bottom: 1px dotted rgba(0,0,0,.2);
        background :#f5f7f9;
        font-size :16px;
        line-height: 20px;
        text-indent: 16px;
        outline: 0;
    }
</style>


<table class="uk-table" id="stock-table">
    <thead>
    <tr>
        <th>#</th>
        <th>股票代码</th>
        <th>股票名称</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $stock)
        <tr>
            <td>{{ $stock['id'] }}</td>
            <td>{{ $stock['code'] }}</td>
            <td>{{ $stock['name'] }}</td>
            <td>@if($stock['status']==0)正常上市@else<span style="color:red">不正常</span>@endif</td>
            <td>
                <button class="uk-button">查看当前策略</button>
                <button class="uk-button" onclick="gotox({{$stock['id']}})">查看金叉</button>
                <button class="uk-button">查看K线图</button>
                @include('stock._saveFav')
                {{--<button class="uk-button uk-button-primary" onclick="save({{$stock['id']}})">收藏</button>--}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{!! $data->links() !!}

@endsection

@section('customerJS')
<script>
    function mytest() {
        if(event.keyCode == 13){
            event.returnValue = false
            event.cancel =true
            searchStock()
        }
    }

    function searchStock() {
        let code = $("#stock-search").val()
        $.get("{{ url('v1/stock/search')}}"+"/"+code, function (res) {
            $("#stock-table tbody tr").remove()
            if(res.fav === 0){
                $("#stock-table").append(`<tr><td>${res.id}</td>
                                        <td>${res.code}</td>
                                        <td>${res.name}</td>
                                        <td>
                                            <button class="uk-button">查看当前策略</button>
                                             <button class="uk-button" onclick="gotox(${res.id})">查看金叉</button>
                                            <button class="uk-button">查看K线图</button>
                                            <button class="uk-button uk-button-primary" onclick="save(${res.id})">收藏</button>
                                        </td>
                                    </tr>`)
            }else{
                $("#stock-table").append(`<tr><td>${res.id}</td>
                                        <td>${res.code}</td>
                                        <td>${res.name}</td>
                                        <td>
                                            <button class="uk-button">查看当前策略</button>
                                             <button class="uk-button" onclick="gotox(${res.id})">查看金叉</button>
                                            <button class="uk-button">查看K线图</button>
                                            <button class="uk-button uk-button-primary" disabled>已收藏</button>
                                        </td>
                                    </tr>`)
            }

        })
    }

    function gotox(id) {
        location.href = "{{url('v1/stock/showChoice')}}"+"/" + id
    }

    function save(id) {
//        let  page = window.location.search
//        page = code.substring(6,code.length)
        let reason = $("#myFav-"+id).val()
        if(!reason){
            reason = prompt('填写收藏理由')
        }

        if(reason != null && reason != ""){
            $.post("{{url('v1/f/save')}}", {id, reason, _token:"{{csrf_token()}}"}, function (res) {
                if(res.errno == 0){
                    UIkit.notify({
                        message : res.msg,
                        status  : 'info',
                        timeout : 2000,
                        pos     : 'top-center'
                    });
                    setTimeout(location.href = location.href)
                }else{
                    alert('收藏失败');
                }
            })
        }else {
            alert('你放弃填写或者填写内容不能为空！')
        }

    }
</script>
@endsection