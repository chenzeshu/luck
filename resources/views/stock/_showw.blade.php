<!-- 触发模态对话框的锚 -->
<a href="#my-week" data-uk-modal class="uk-button  uk-button-primary">查看<b>周</b>金叉</a>

<!-- 模态对话框 -->
<div id="my-week" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
            <table class="uk-table">
                <thead>
                    <th>日期</th>
                    <th>MACD值</th>
                </thead>
                <tbody>
                    @foreach($data as $v)
                        <td>{{$data['date']}}</td>
                        <td>{{$data['macd']}}</td>
                    @endforeach
                </tbody>
            </table>
    </div>
</div>