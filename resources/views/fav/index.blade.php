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
        .stock-warn{
            color:red;
            font-weight: 700;
        }
    </style>

    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed" id="stock-table">
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
                @if(count($stock['monthxes']) != 0)
                    <td class="stock-warn">{{ $stock['stock']['code'] }}</td>
                    <td class="stock-warn">{{ $stock['stock']['name'] }}</td>
                @else
                    <td>{{ $stock['stock']['code'] }}</td>
                    <td>{{ $stock['stock']['name'] }}</td>
                @endif
                <td>{{ $stock['reason'] }}</td>
                <td>{{ $stock['stock']['macd_max'] }}</td>
                @if(count($stock['monthxes']) != 0)
                <td class="stock-warn">{{ $stock['monthxes'][0]['date']}} : {{ $stock['monthxes'][0]['macd'] }}</td>
                @else
                <td>最近叉是死叉</td>
                @endif
                @if(count($stock['weekxes']) != 0)
                    <td>{{ $stock['weekxes'][0]['date'] }} : {{ $stock['weekxes'][0]['macd'] }}</td>
                @else
                    <td>最近叉是死叉</td>
                @endif
                <td>
                    @include('strategy._create')
                    @include('strategy._createTime')
                    <button class="uk-button uk-button-danger" onclick="deleteFav( {{$stock['id']}}, '{{ $stock['stock']['name'] }}')">取消收藏</button>
                    <button class="uk-button" onclick="toDong('{{$stock['stock']['code']}}')">东方财富通→</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section('customerJS')
    <script>
        /**
         * 取消收藏
         */
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

        /**
         * 新建macd值策略
         * @param id
         */
        function createValue(id) {
            let msg = $("#myValueMsg-"+id).val()
            let val = $("#myValue-"+id).val()

            if(!val){
                alert('填写要提醒的macd值')
                return
            }

            if(val != null && val != ""){
                $.post("{{url('v1/s/v/create')}}", {id, msg, val, _token:"{{csrf_token()}}"}, function (res) {

                    if(res.errno == 0){
                        UIkit.modal.confirm(`${res.msg}, 是否去策略页？`, function(){
                            // 点击OK确认后开始执行
                            location.href = "{{url('v1/s/v/1/10')}}"
                        }, function () {
                            location.href = location.href
                        });
                    }else{
                        alert('存入策略失败，请联系开发者！');
                    }
                }).error(function (xhr, errorText, errorType) {
                    confirm('存入策略失败！可能你之前已存储了策略，或请联系开发者')
                })
            }else {
                alert('你放弃填写macd值或者macd值不能为空！')
            }
        }

        /**
         * 新建时间策略
         * @param id
         */
        function createTime(id) {
            let msg = $("#myTimeMsg-"+id).val()
            let val = $("#myTime-"+id).val()

            if(!val){
                val = prompt('填写要提醒的时间')
            }

            if(val != null && val != ""){
                $.post("{{url('v1/s/t/create')}}", {id, msg, val, _token:"{{csrf_token()}}"}, function (res) {

                    if(res.errno == 0){
                        UIkit.modal.confirm(`${res.msg}, 是否去策略页？`, function(){
                            // 点击OK确认后开始执行
                            location.href = "{{url('v1/s/t/1/10')}}"
                        }, function () {
                            location.href = location.href
                        });
                    }else{
                        alert('存入策略失败，请联系开发者！');
                    }
                }).error(function (xhr, errorText, errorType) {
                    confirm('存入策略失败！可能你之前已存储了策略，或请联系开发者')
                })
            }else {
                alert('你放弃填写时间或者时间不能为空！')
            }
        }
    </script>
@endsection