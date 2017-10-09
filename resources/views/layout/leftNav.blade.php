<style>
    #leftNav{
        font-size: 18px;
        line-height: 32px;
    }
    table{
        font-size: 18px;
    }
</style>

<ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-width-medium-2-3" data-uk-nav id="leftNav">
    <li><a href="{{url('v1/stock/index')}}">股票列表</a></li>

    <li class="uk-parent">
        <a href="#">我的</a>
        <ul class="uk-nav-sub">
            <li><a href="{{url('v1/f/getdata/1/15')}}">我的收藏</a></li>
            <li><a href="{{url('v1/msg/unread/1/10')}}">我的消息 @if(getC('msg_num') != 0)<div class="uk-badge uk-badge-danger">{{getC('msg_num')}}</div>@endif</a></li>
            <li><a href="#">我的策略</a>
                <ul>
                    <li><a href="{{url('v1/s/v/1/10')}}">按值提醒</a></li>
                    <li><a href="{{url('v1/s/t/1/10')}}">按时间提醒</a></li>
                </ul>
            </li>
        </ul>
    </li>

    <li class="uk-parent">
        <a href="#">推荐</a>
        <ul class="uk-nav-sub">
            <li><a href="{{url('v1/rec/getmonth/1/15')}}">月金叉股</a></li>
            <li><a href="{{url('v1/rec/getweek/1/15')}}">周金叉股</a></li>
            <li><a href="{{url('v1/rec/getmul/1/15')}}">月x+周x</a></li>
        </ul>
    </li>
</ul>