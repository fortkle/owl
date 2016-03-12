@extends('layouts.master')

@section('title')
お気に入り一覧 | Owl
@stop

@section('navbar-menu')
    @include('layouts.navbar-menu')
@stop

@section('contents-pagehead')
<p class="page-title">お気に入り一覧</p>
@stop

@section('contents-main')

<div class="page-header">
    <h5>最近のお気に入り</h5>
</div>

<div class="stocks">
    @forelse ($stocks as $stock)
    <div class="stock">
        {!! HTML::gravator($stock->email, 40) !!}
        <p><a href="/{{{$stock->username}}}" class="username">{{{$stock->username}}}</a>さんが<?php echo date('Y/m/d', strtotime($stock->updated_at)); ?>に投稿しました。</p>
        <p><a href="{{ action('ItemController@show', $stock->open_item_id) }}"><strong>{{{ $stock->title }}}</strong></a></p>
    </div>
    @empty
      <p class="text-center text-muted"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> お気に入りされた投稿はありません。</p>
    @endforelse
    <?php echo $stocks->render(); ?>
</div>
@stop

@section('contents-sidebar')
    @include('layouts.contents-sidebar')
@stop
