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
<input type="text" placeholder="填写股票代码" id="code">
<button onclick="upload()">提交</button>

<div id="main" style="width: 1200px;height:800px;"></div>
<table id="alert" class="table table-bordered table-condensed" width="400px">
    <tr class="active">
        <td><b>类型</b></td>
        <td><b>时间(取10个)</b></td>
    </tr>
</table>

<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript">
    function upload() {
        var codeEl = document.getElementById('code')
        var code = codeEl.value
        if(checkCode(code)){
            getChart(code)
        }else{
            console.log('代码错误')
        }

    }

    function checkCode(code) {
        if(code.length !== 6){
            return false
        }else {
            return true
        }
    }

    function getChart(code, begin, end) {
        let begin = "20150227"
        let end = "20170930"
        let url = `http://q.stock.sohu.com/hisHq?code=cn_${code}&start=${begin}&end=${end}&stat=1&order=D&period=d&callback=historySearchHandler&rt=jsonp`

        function myCallBack(result){

        }

        $.ajax({
            url:url,  //一定要有http
            dataType:"jsonp",
            jsonp:"callback",  //后台$_GET[]取ndler", //键值对的值
            jsonpCallback:"historySearchHandler",
            success:function(res){
                let data = res[0].hq.reverse()
                let price = [], date = [], EMA12=[],EMA26=[], DIFF=[], DEA=[], MACD=[]
                date[0] = data[0][0]
                EMA12[0] =0, EMA26[0] = 0  //第一天等于收盘价
                DIFF[0] = 0, DEA[0] = 0, MACD[0] = 0
                let ema12a = 2/13, ema12b = 11/13, ema26a = 2/27, ema26b = 25/27, deaa= 8/10, deab = 2/10
                let length = data.length -1
                for(let i = 1; i < length ; i++){
                    date.push(data[i][0])
                    //todo get EMA12 & EMA26
                    EMA12.push(ema12a * data[i][2] + ema12b * EMA12[i-1])
                    EMA26.push(ema26a * data[i][2] + ema26b * EMA26[i-1])
                    //todo get DIFF & DEA
                    DIFF.push(EMA12[i] - EMA26[i])
                    DEA.push(deaa * DEA[i-1] + deab * DIFF[i])
                    //todo calculate MACD
                    MACD.push( 2 * (DIFF[i] - DEA[i]))
                }

                //todo 数据不齐报警模块
                let days1, days2
                //todo 求得这段时间的总天数
                days1 = new Date(date[0])
                days2 = new Date(date[date.length-1])
                let day = Math.ceil((days2-days1)/1000/86400)
                //todo 求得这段时间的非开盘日期
                let years = Math.ceil(day/365)
                let remove = years * 115

                //那么数据差不能超过(week * 2.4), 一年法定假日大约11天, 一年总共休息日为115天

                console.log(day, date.length, `差值:${day - date.length - remove}`, `非开盘日:${remove}`)
                if(day - date.length  > remove){
                    console.log('数据不齐较严重!')
                }else {
                    console.log('数据完整度尚可')
                }

                let x = []
                for(var i = 1; i < (date.length -1); i++){
                    //todo 计算金叉
                    if(DIFF[i] > DEA[i] && DIFF[i-1] < DEA[i-1] ){
                        x.push({type:"金X", date:date[i-1]})
                    }
                    //todo 计算死叉
//                    if(DIFF[i] < DEA[i] && DIFF[i-1] > DEA[i-1] ){
//                        x.push({type:"死叉", date:date[i-1]})
//                    }
                }

                x = x.slice(x.length -1, x.length)

                var alert = document.getElementById('alert')
                for(var n = 0; n< x.length ; n++){
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
                        data:['MACD','DIFF', 'DEA']
                    },
                    xAxis: {
                        data: date
                    },
                    yAxis: {},
                    series: [
                        {
                            name: 'MACD',
                            type: 'bar',
                            data: MACD
                        },
                        {
                            name: 'DIFF',
                            type: 'line',
                            data: DIFF
                        },
                        {
                            name: 'DEA',
                            type: 'line',
                            data: DEA
                        }
                    ]
                };

                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option);
            }

        })
    }
</script>
</body>
</html>