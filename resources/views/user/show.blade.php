@extends('layouts.master')

@section('title')
{{ $user->username }} | Owl
@stop

@section('navbar-menu')
    @include('layouts.navbar-menu')
@stop

@section('contents-pagehead')
<p class="page-title">アカウント</p>
@stop

@section('contents-main')

    <div class="page-header">
        <h5>プロフィール</h5>
    </div>

    <div class="media">
        <a class="pull-left" href="#">
        {!! HTML::gravator($user->email, 80,'mm','g','true',array('class'=>'media-object')) !!}
        </a>
        <div class="media-body">
            <h4 class="media-heading">{{{ $user->username }}}</h4>
        </div>
    </div>

    <div class="page-header">
        <h5>最近の投稿</h5>
    </div>

    <div class="items">
        @forelse ($items as $item)
        <div class="item">
            {!! HTML::gravator($item->user->email, 40) !!}
            <p><a href="/{{{$item->user->username}}}" class="username">{{{$item->user->username}}}</a>さんが<?php echo date('Y/m/d', strtotime($item->updated_at)); ?>に投稿しました。</p>
            <p><a href="{{ action('ItemController@show', $item->open_item_id) }}"><strong>{{{ $item->title }}}</strong></a></p>
        </div>
        @empty
            <p class="text-center text-muted"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> 最近の投稿はありません。</p>
        @endforelse
    <?php echo $items->render(); ?>
    </div>

@stop

@section('contents-sidebar')
    @include('layouts.contents-sidebar')
@stop
