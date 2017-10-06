@extends('layout.layout')

@section('content')
    <article class="uk-article">
        <h1 class="uk-article-title">{{$data['name']}}</h1>
        <p class="uk-article-meta">{{$data['code']}}</p>
        <p class="uk-article-lead"><button class="uk-button" onclick="returnToList()">返回列表</button></p>

        <hr class="uk-article-divider">
        <div class="table-wrapper">
            <table class="uk-table">
                <tr>
                    <td>
                        @include("stock._showm")
                    </td>
                </tr>
                <tr>
                    <td>
                        @include("stock._showw")
                    </td>
                </tr>
                <tr>
                    <td>
                        @include("stock._showd")
                    </td>
                </tr>
            </table>
        </div>
    </article>

@endsection