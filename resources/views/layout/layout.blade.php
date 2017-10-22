<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>stocks</title>
    <link href="{{asset('css/uikit.almost-flat.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/components/notify.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/components/form-select.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/components/datepicker.min.css')}}" rel="stylesheet">
    <style>
        .save-fav, .strategy-input{
            display: inline-block;
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
        .strategy-input{
            height : 40px;
        }
        .save-fav{
            height : 100px;
        }
        .m-date-picker{
            display: inline-block;
            margin-right: 10px;
            padding-left: 10px;
            width: 240px;
            height: 30px;
            border-radius: 5px;
            outline: 0;
            border: 1px solid rgb(200,200,200);
            font-size: 18px;
        }
        .strategy-input{
            display: inline-block;
            height: 18px;
            width: 16vw;
            margin: 0 15px 15px 0;
            padding-top: 8px;
            border-bottom: 1px dotted rgba(0,0,0,.2);
            background: #f5f7f9;
            font-size: 18px;
            line-height: 16px;
            text-indent: 16px;
            outline: 0;
            vertical-align: middle;
        }
    </style>
</head>
<body>

@include('layout.header')

<div class="uk-grid" data-uk-grid-margin>
    <div class="uk-width-medium-1-4">
        @include('layout.leftNav')
    </div>
    <div class="uk-width-medium-3-4">
        @yield('content')
    </div>

</body>

<script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="{{asset('js/uikit.mymin.js')}}"></script>   {{--自行压缩uikit.js，因为uikit.min.js做了代码混淆，很难改--}}
<script src="{{asset('js/components/notify.min.js')}}"></script>
<script src="{{asset('js/components/form-select.min.js')}}"></script>
<script src="{{asset('js/components/datepicker.js')}}"></script>
<script src="{{asset('js/components/pagination.js')}}"></script>
<!-- 引入 echarts.js -->
{{--<script src="{{asset('js/echarts.js')}}"></script>--}}
@yield('customerJS')
<script>
    navActive()
    initNavActive()
    /**
     *  跳转到东方财富通
     */
    function toDong(code) {
        if(code.substring(0,1) == 6){
            location.href = "http://quote.eastmoney.com/sh"+code+".html"
        }else{
            location.href = "http://quote.eastmoney.com/sz"+code+".html"
        }
    }


    function returnToList() {
        location.href = "{{url('v1/stock/index')}}"
    }

    function navActive() {
        $("#leftNav>li").each(function (index, e) {
            e.addEventListener('click', function () {
                localStorage.setItem('__leftNav__', index)
                $("#leftNav>li").each(function (index, e) {
                    $(e).removeClass('uk-active')
                })
                $(e).addClass('uk-active')
            })
        })
    }
    
    function initNavActive() {
        let index = localStorage.getItem('__leftNav__')
        if(!index){
            index = 0
        }
        $("#leftNav>li").each(function (index, e) {
            $(e).removeClass('uk-active')
        })
        $("#leftNav>li:eq("+index+")").addClass('uk-active')
    }
    /**
     * 加入收藏
     * @param id
     */
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
                    setTimeout(function () {
                        location.href=location.href
                    }, 1500)
                }else{
                    alert('收藏失败');
                }
            })
        }else {
            alert('你放弃填写或者填写内容不能为空！')
        }
    }
</script>
</html>