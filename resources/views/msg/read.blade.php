@extends('layout.layout')

@section('content')
    <a href="{{url('v1/msg/unread/1/10')}}" class="uk-button">
        <i class="uk-icon-map-o"></i>&nbsp;未读消息   &nbsp;
        <div class="uk-badge uk-badge-danger">{{getC('msg_num')}}</div>
    </a><a href="{{url('v1/msg/read/1/10')}}" class="uk-button uk-button-primary"><i class="uk-icon-map-o"></i>&nbsp;已读消息</a>

    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
        <thead>
            <th>股票代码</th>
            <th>事件</th>
            <th>内容</th>
            <th>发生时间</th>
        </thead>
        <tbody>
        @if(count($data))
            @foreach($data as $msg)
                <tr>
                    <td>{{$msg['code']}}</td>
                    <td>{{$msg['type']}}</td>
                    <td>{{$msg['desc']}}</td>
                    <td>{{$msg['created_at']}}</td>
                    <td><button class="uk-button uk-button-danger" onclick="delMsg({{$msg['id']}})">删除</button></td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    <ul class="uk-pagination">
        <li><a href=""><i class="uk-icon-angle-double-left"></i></a></li>
        @foreach(range(1, $pageCount) as $num)
            @if($num == $curPage)
                <li class="uk-active"><span>{{$num}}</span></li>
            @else
                <li class="uk-disabled"><a href="{{url('v1/msg/read')."/".$num."/10"}}"><span>{{$num}}</span></a></li>
            @endif
        @endforeach
        <li><a href=""><i class="uk-icon-angle-double-right"></i></a></li>
    </ul>
@endsection

@section('customerJS')
    <script>
        function delMsg(id) {
            if(prompt('是否删除？')){
                $.get("{{url('v1/msg/delete')}}/"+id, function (res) {
                    UIkit.notify({
                        message : res.msg,
                        status  : 'info',
                        timeout : 1500,
                        pos     : 'top-center'
                    });
                    setTimeout(function () {
                        location.href = location.href
                    },1500)
                })
            }
        }
    </script>
@endsection