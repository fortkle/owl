@extends('layouts.master')

@section('title')
ストック一覧 | Owl
@stop

@section('navbar-menu')
    @include('layouts.navbar-menu')
@stop

@section('contents-pagehead')
@if (is_null($q) || $q === "") 
<p class="page-title">ストック一覧 - 検索ワードが設定されていません。</p>
@else
<p class="page-title">ストック一覧 - <span class='search-word'>{{$q}}</span>の検索結果</p>
@endif
@stop
@stop

@section('contents-main')
<div class="page-header stock-search" >
    <h5>最近のストック</h5>
    <div class="form-group">
        <form class="navbar-form navbar-left"role="search" method="GET" action="/stocks/search">
            <input name="q" value="{{isset($q)? $q : ''}}" type="text" class="form-control" placeholder="Search">
            <button type="submit" class="btn btn-default">検索</button>
        </form>
    </div>
    <br clear='both' />
</div>
<div class="stocks">
    @if (count($stocks) > 0)
    @foreach ($stocks as $stock)
    <div class="stock">
        {{ HTML::gravator($stock->email, 40) }}
        <p><a href="/{{{$stock->username}}}" class="username">{{{$stock->username}}}</a>さんが<?php echo date('Y/m/d', strtotime($stock->updated_at)); ?>に投稿しました。</p>
        <p><a href="{{ action('ItemController@show', $stock->open_item_id) }}"><strong>{{{ $stock->title }}}</strong></a></p>
    </div>
    @endforeach
    <?php echo $pagination->appends(array('q' => $q))->links(); ?>
    @else
    <div class="noresults">
    検索結果は見つかりませんでした。検索ワードを変えて再度検索して下さい。
    </div> 
    @endif
</div>
@stop

@section('contents-sidebar')
    @include('layouts.contents-sidebar')
@stop
