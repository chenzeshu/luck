@extends('layout.layout')

@section('content')
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
    </style>

    <table class="uk-table" id="stock-table">
        <thead>
        <tr>
            <th>股票代码</th>
            <th>股票名称</th>
            <th>收藏原因</th>
            <th>历史最大macd值</th>
            <th>最近月金X ：值</th>
            <th>最近周金X : 值</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $stock)
            <tr>
                <td>{{ $stock['stock']['code'] }}</td>
                <td>{{ $stock['stock']['name'] }}</td>
                <td>{{ $stock['reason'] }}</td>
                <td>{{ $stock['stock']['macd_max'] }}</td>
                @if(count($stock['monthxes']) != 0)
                {{--<td>{{ substr($stock['monthxes'][0]['date'], 0, 10) }} : {{ $stock['monthxes'][0]['macd'] }}</td>--}}
                <td>{{ $stock['monthxes'][0]['date']}} : {{ $stock['monthxes'][0]['macd'] }}</td>
                @else
                <td>无</td>
                @endif
                @if(count($stock['weekxes']) != 0)
                    {{--<td>{{  substr($stock['weekxes'][0]['date'],0, 10)}} : {{ $stock['weekxes'][0]['macd'] }}</td>--}}
                    <td>{{ $stock['weekxes'][0]['date'] }} : {{ $stock['weekxes'][0]['macd'] }}</td>
                @else
                    <td>无</td>
                @endif
                <td>
                    <button class="uk-button uk-button-danger" onclick="deleteFav( {{$stock['id']}}, '{{ $stock['stock']['name'] }}')">取消收藏</button>
                    <button class="uk-button uk-button-primary">查看东方财富通</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection

@section('customerJS')
    <script>
        function deleteFav(id , name) {
            let check = confirm(`是否取消收藏【${name}】?`);
            if(check){
                $.get("{{url('v1/f/delete')}}/"+id, function (res) {
                    if(res.errno === 0){
                        UIkit.notify({
                            message : res.msg,
                            status  : 'info',
                            timeout : 1500,
                            pos     : 'top-center'
                        });
                      setTimeout(function () {
                          location.href=location.href
                      }, 1500)
                    }else{
                       alert(res.msg)
                    }
                })
            }

        }
    </script>
@endsection