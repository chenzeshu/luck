<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>stocks</title>
    <link href="{{asset('css/uikit.almost-flat.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/components/notify.min.css')}}" rel="stylesheet">
    <!-- 引入 echarts.js -->

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

<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="{{asset('js/uikit.min.js')}}"></script>
<script src="{{asset('js/components/notify.min.js')}}"></script>
<script src="{{asset('js/echarts.js')}}"></script>
@yield('customerJS')
<script>
    function returnToList() {
        location.href = "{{url('v1/stock/index')}}"
    }
</script>
</html>