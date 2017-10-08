<!-- 触发模态对话框的锚 -->
<a href="#editVT-{{$stock['id']}}" data-uk-modal class="uk-button uk-button-primary">修改时间策略</a>

<!-- 模态对话框 -->
<div id="editVT-{{$stock['id']}}" class="uk-modal">
    <div class="uk-modal-dialog">
        <textarea placeholder="填写发送手机的消息" id="editVTmsg-{{$stock['id']}}" class="save-fav">{{$stock['msg']}}</textarea><br>
        <input type="" data-uk-datepicker="{format:'YYYY-MM-DD'}" value="{{substr($stock['refertime'],0,10)}}"
               placeholder="选择需要提醒的日期" id="editVTvalue-{{$stock['id']}}" class="strategy-input" style="display: inline-block;vertical-align: top">
        <button class="uk-button uk-button-primary" onclick="editValue({{$stock['id']}})" style="display: inline-block;vertical-align: middle" >确定</button>
        <a class="uk-modal-close uk-close"></a>
    </div>
</div>