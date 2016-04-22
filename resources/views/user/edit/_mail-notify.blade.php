{{-- for /user/edit page --}}
{{-- injected by ViewComposer --}}

<div class="page-header">
  <h5>通知メール送信設定</h5>
</div>

<p>
  それぞれの項目に対してメールで通知するかどうか設定できます。
</p>

<div class="form-group form-horizontal">
  <div class="form-group">
    {!! Form::label('comment-mail-checkbox', 'コメントがついた時', array('class' => 'col-sm-4 control-label')) !!}
    <div class="col-sm-3">
      {!! Form::checkbox('comment-mail-checkbox', 'comment', !!$notifyFlags->comment_notification_flag) !!}
    </div>
  </div>

  <div class="form-group mail-checkbox-form">
    {!! Form::label('favorite-mail-checkbox', '記事がお気に入りされた時', array('class' => 'col-sm-4 control-label')) !!}
    <div class="col-sm-3">
      {!! Form::checkbox('favorite-mail-checkbox', 'favorite', !!$notifyFlags->favorite_notification_flag) !!}
    </div>
  </div>

  <div class="form-group mail-checkbox-form">
    {!! Form::label('like-mail-checkbox', 'いいねがついた時', array('class' => 'col-sm-4 control-label')) !!}
    <div class="col-sm-3">
      {!! Form::checkbox('like-mail-checkbox', 'like', !!$notifyFlags->like_notification_flag) !!}
    </div>
  </div>

  <div class="form-group mail-checkbox-form">
    {!! Form::label('edit-mail-checkbox', '自分の記事が編集された時', array('class' => 'col-sm-4 control-label')) !!}
    <div class="col-sm-3">
      {!! Form::checkbox('edit-mail-checkbox', 'edit', !!$notifyFlags->edit_notification_flag) !!}
    </div>
  </div>
</div>
