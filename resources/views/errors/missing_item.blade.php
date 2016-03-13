@extends('layouts.master')

@section('title')
404 Not Found | Owl
@stop

@section('navbar-menu')
    @include('layouts.navbar-menu')
@stop

@section('contents-pagehead')
<p class="page-title">404 Not Found</p>
@stop

@section('contents-main')
    <h4>お探しの記事は見つかりませんでした</h4>
@stop
