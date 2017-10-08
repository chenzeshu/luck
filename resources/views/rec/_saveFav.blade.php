<!-- 触发模态对话框的锚 -->
@if($stock['stock']['fav']==0)
    <a href="#saveFav-{{$stock['stock_id']}}" data-uk-modal class="uk-button uk-button-primary">收藏</a>
@else
    <button data-uk-modal class="uk-button uk-button-primary" disabled>已收藏</button>
@endif
<!-- 模态对话框 -->
<div id="saveFav-{{$stock['stock_id']}}" class="uk-modal">
    <div class="uk-modal-dialog">
        <textarea placeholder="填写收藏理由" id="myFav-{{$stock['stock']['id']}}" class="save-fav"></textarea><br>
        <button class="uk-button uk-button-primary" onclick="save({{$stock['stock_id']}})">确定</button>
        <a class="uk-modal-close uk-close"></a>
    </div>
</div>