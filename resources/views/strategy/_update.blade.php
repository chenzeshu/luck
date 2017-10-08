<!-- 触发模态对话框的锚 -->
<a href="#editVS-{{$stock['id']}}" data-uk-modal class="uk-button uk-button-primary">修改值策略</a>

<!-- 模态对话框 -->
<div id="editVS-{{$stock['id']}}" class="uk-modal">
    <div class="uk-modal-dialog">
        <textarea placeholder="填写发送手机的消息" id="editVSmsg-{{$stock['id']}}" class="save-fav">{{$stock['msg']}}</textarea><br>
        <input type="text" value="{{$stock['value']}}" name="value"
               placeholder="填写需要提醒的macd数值" id="editVSvalue-{{$stock['id']}}" class="strategy-input" style="display: inline-block;vertical-align: top">
        <button class="uk-button uk-button-primary" onclick="editValue({{$stock['id']}})" style="display: inline-block;vertical-align: middle" >确定</button>
        <a class="uk-modal-close uk-close"></a>
    </div>
</div>
