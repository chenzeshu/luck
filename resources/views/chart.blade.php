<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ECharts</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.bootcss.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
    <!-- 引入 echarts.js -->
    <script src="{{asset('js/echarts.js')}}"></script>
</head>
<body>
<style>
    #alert{
        text-align: center;
        position:absolute;
        top:900px;
        left:0;
    }
</style>
<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div id="main" style="width: 1200px;height:800px;"></div>
<table id="alert" class="table table-bordered table-condensed" width="400px">
    <tr class="active">
        <td><b>类型</b></td>
        <td><b>时间(取10个)</b></td>
    </tr>
</table>

<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript">
    var code = "300455"
    var date = []
    var diff = []
    var dea = []
    var macd = []
    var x = []

    $.ajax({
        url:"{{url("diff")}}"+"/"+code,
        type:"get",
        success:function (res) {
           date = res.date
           diff = res.diff
           dea = res.dea
           macd = res.macd
            for(var i = 1; i < (date.length -1); i++){
                //todo 计算金叉
                if(diff[i] > dea[i] && diff[i-1] < dea[i-1] ){
                    x.push({type:"金叉", date:date[i]})
                }
                //todo 计算死叉
                if(diff[i] < dea[i] && diff[i-1] > dea[i-1] ){
                    x.push({type:"死叉", date:date[i]})
                }
            }
            x = x.slice(x.length - 11,x.length)

            var alert = document.getElementById('alert')
            for(var n = 0; n< (x.length -1) ; n++){
                var trElement = document.createElement('tr')
                var tdElement = document.createElement('td')
                var td2Element = document.createElement('td')
                var tdText = document.createTextNode(x[n].type)
                var td2Text = document.createTextNode(x[n].date)
                tdElement.appendChild(tdText)
                td2Element.appendChild(td2Text)
                trElement.appendChild(tdElement)
                trElement.appendChild(td2Element)
                alert.appendChild(trElement)
            }


            // 基于准备好的dom，初始化echarts实例
            var myChart = echarts.init(document.getElementById('main'));

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: code
                },
                tooltip: {},
                legend: {
                    data:['macd','diff', 'dea']
                },
                xAxis: {
                    data: date
                },
                yAxis: {},
                series: [
                    {
                        name: 'macd',
                        type: 'bar',
                        data: macd
                    },
                    {
                        name: 'diff',
                        type: 'line',
                        data: diff
                    },
                    {
                        name: 'dea',
                        type: 'line',
                        data: dea
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
        }
    })



</script>
</body>
</html>