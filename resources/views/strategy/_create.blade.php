<!-- 触发模态对话框的锚 -->
@if(count($stock['my_values']) >0 )
    <button class="uk-button" disabled>已建值策略</button>
@else
    <a href="#createValue-{{$stock['id']}}" data-uk-modal class="uk-button uk-button-primary">新建值策略</a>
@endif
<!-- 模态对话框 -->
<div id="createValue-{{$stock['id']}}" class="uk-modal">
    <div class="uk-modal-dialog">
        <textarea placeholder="填写发送手机的消息" id="myValueMsg-{{$stock['id']}}" class="save-fav"></textarea><br>
        <input type="text" value="" name="value" placeholder="填写需要提醒的macd数值" id="myValue-{{$stock['id']}}" class="strategy-input" style="display: inline-block;vertical-align: top">
        <button class="uk-button uk-button-primary" onclick="createValue({{$stock['id']}})" style="display: inline-block;vertical-align: middle" >确定</button>
        <a class="uk-modal-close uk-close"></a>
    </div>
</div>