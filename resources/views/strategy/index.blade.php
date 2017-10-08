@extends('layout.layout')

@section('content')
    <h2>值策略列表</h2>

<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
    <thead>
        <th>#</th>
        <th>股票代码</th>
        <th>股票名称</th>
        <th>历史最大macd值</th>
        <th>提醒值</th>
        <th>预设消息</th>
        <th>操作</th>
    </thead>
    <tbody>
    @if(count($data)>0)
        @foreach($data as $stock)
        <tr>
           <td>{{$stock['id']}}</td>
           <td>{{$stock['favorite']['stock']['code']}}</td>
           <td>{{$stock['favorite']['stock']['name']}}</td>
           <td>{{$stock['favorite']['stock']['macd_max']}}</td>
           <td>{{$stock['value']}}</td>
           <td>{{$stock['msg']}}</td>
           <td>
              @include('strategy._update')
               <button class="uk-button uk-button-danger" onclick="delVS({{$stock['id']}})">删除策略</button>
               @if($stock['known']===0)
                    <button class="uk-button uk-button-primary" onclick="known({{$stock['id']}})">不再通知</button>
               @else
                    <button class="uk-button" disabled>不再通知</button>
               @endif
           </td>
        </tr>
        @endforeach
    @endif
    </tbody>
</table>

<ul class="uk-pagination">
    @if($curPage > 1)
        <li><a href="{{url('v1/s/v')."/".($curPage - 1)."/10"}}"><i class="uk-icon-angle-double-left"></i></a></li>
    @else
        <li><a href=""><i class="uk-icon-angle-double-left"></i></a></li>
    @endif

    @foreach(range(1, $pageCount) as $num)
        @if($num == $curPage)
            <li class="uk-active"><span>{{$num}}</span></li>
        @elseif(  abs($num - $curPage) < 3 || $num < 3 || $pageCount - $num < 2 )
            <li class="uk-disabled"><a href="{{url('v1/s/v/')."/".($num)."/10"}}"><span>{{$num}}</span></a></li>
        @elseif( abs($num - $curPage) > 3 )
            ...
        @elseif(abs($num - $curPage) == 3)
            <li><a href="#"><span>...</span></a></li>
        @else
            <li class="uk-disabled"><a href="{{url('v1/s/v')."/{$num}/10/"}}"><span>{{$num}}</span></a></li>
        @endif
    @endforeach

    @if($curPage == $pageCount )
        <li><a href=""><i class="uk-icon-angle-double-right"></i></a></li>
    @else
        <li><a href="{{url('v1/s/v')."/".($curPage + 1)."/10"}}"><i class="uk-icon-angle-double-right"></i></a></li>
    @endif
</ul>
@endsection

@section('customerJS')
    <script>
        function editValue(id) {
            let msg = $("#editVSmsg-"+id).val()
            let val = $("#editVSvalue-"+id).val()

            if(!val){
                alert('必须填写要提醒的macd值')
                return
            }

            if(val != null && val != ""){
                $.post("{{url('v1/s/v/update')}}", {id, msg, val, _token:"{{csrf_token()}}"}, function (res) {
                    if(res.errno == 0){
                        UIkit.notify({
                            message : res.msg,
                            status  : 'info',
                            timeout : 1500,
                            pos     : 'top-center'
                        })
                        setTimeout(function () {
                            location.href = location.href
                        }, 1500)
                    }else{
                        alert('修改策略失败，请联系开发者！');
                    }
                }).error(function (xhr, errorText, errorType) {
                    confirm('修改策略失败！可能你之前已存储了策略，或请联系开发者')
                })
            }else {
                alert('你放弃填写macd值或者macd值不能为空！')
            }
        }

        function delVS(id) {
            let check = confirm("是否真的要删除"+id+"号策略?")
            if(!check){
                return
            }
            $.get("{{url('v1/s/v/delete')}}/"+id, function (res) {
               if(res.errno == 0){
                   UIkit.notify({
                       message : res.msg,
                       status  : 'info',
                       timeout : 1500,
                       pos     : 'top-center'
                   })
                   setTimeout(function () {
                       location.href = location.href
                   }, 1500)
               }else {
                   alert('删除策略失败，请联系开发者！');
               }
            }).error(function (xhr, errorText, errorType) {
                confirm('删除策略失败！可能你之前已存储了策略，或请联系开发者')
            })
        }

        function known(id) {
            let check = confirm("确定不再提醒"+id+"号策略?")
            if(!check){
                return
            }

            $.get("{{url('v1/s/v/known')}}/"+id, function (res) {
                if(res.errno == 0){
                    UIkit.notify({
                        message : res.msg,
                        status  : 'info',
                        timeout : 1500,
                        pos     : 'top-center'
                    })
                    setTimeout(function () {
                        location.href = location.href
                    }, 1500)
                }else {
                    alert('遮蔽策略失败，请联系开发者！');
                }
            }).error(function (xhr, errorText, errorType) {
                confirm('遮蔽策略失败！请联系开发者')
            })
        }
    </script>
@endsection