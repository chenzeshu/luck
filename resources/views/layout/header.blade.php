<div class="uk-margin">
    <nav class="uk-navbar">
        <a class="uk-navbar-brand" href="#">Stock</a>

        <div class="uk-navbar-content uk-navbar-flip  uk-hidden-small">
            <div class="uk-button-group">
                <a class="uk-button uk-button-primary" href="{{url('v1/msg/unread/1/10')}}">我的消息
                &nbsp;@if(getC('msg_num') != 0)
                    <div class="uk-badge uk-badge-danger">{{getC('msg_num')}}</div>
                  @endif
                </a>
                <button class="uk-button uk-button-primary"></button>
            </div>
        </div>
    </nav>
</div>


