<!-- 触发模态对话框的锚 -->
@if(count($stock['my_times']) !== 0)
    <button class="uk-button" disabled>已建时间策略</button>
@else
    <a href="#createTime-{{$stock['id']}}" data-uk-modal class="uk-button uk-button-primary">新建时间策略</a>
@endif
<!-- 模态对话框 -->
<div id="createTime-{{$stock['id']}}" class="uk-modal">
    <div class="uk-modal-dialog">
        <textarea placeholder="填写发送手机的消息" id="myTimeMsg-{{$stock['id']}}" class="save-fav"></textarea><br>
        <input type="" data-uk-datepicker="{format:'YYYY-MM-DD'}" placeholder="选择需要提醒的日期" id="myTime-{{$stock['id']}}" class="strategy-input" style="display: inline-block;vertical-align: top">
        <button class="uk-button uk-button-primary" onclick="createTime({{$stock['id']}})" style="display: inline-block;vertical-align: middle">确定</button>
        <a class="uk-modal-close uk-close"></a>
    </div>
</div>