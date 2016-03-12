@extends('layouts.master')

@section('title')
テンプレート一覧 | Owl
@stop

@section('navbar-menu')
    @include('layouts.navbar-menu')
@stop

@section('contents-pagehead')
<p class="page-title">テンプレート一覧</p>
@stop

@section('contents-main')
    <p>テンプレートは全体で共通です。</p>

    <a href="/templates/create"><button type="button" class="btn btn-success btn-sm">新規作成</button></a><br />

    <br />
    <table class="table table-bordered">
        <tr>
            <th>テンプレート名</th>
            <th>タイトル</th>
            <th>操作</th>
        </tr>
        @forelse ($templates as $template)
        <tr>
            <td>{{{ $template->display_title }}}</a></td>
            <td>{{{ $template->title }}}</td>
            <td>
                {!! Form::open(['route'=>['templates.destroy', $template->id], 'method'=>'DELETE']) !!}
                <a href="/templates/{{$template->id}}/edit"><button type="button" class="btn btn-default btn-sm">編集</button></a>
                <button onClick="return confirm('本当に削除しますか？');" type="submit" class="btn btn-danger btn-sm">削除</button>
                {!! Form::close() !!}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan='3' class="text-center text-muted"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> 表示するテンプレートはありません。</td>
        </tr>
        @endforelse
    </table>
@stop

@section('contents-sidebar')
    @include('layouts.contents-sidebar')
@stop
