/**
 * switch.js
 *
 * Description:
 *   ユーザ設定画面のbootstrap-switch用jsファイル
 *
 * Author:
 *   @sota1235
 */

$(function() {
  // bootstrap-switch適用対象チェックボックス
  var targets = [
    'comment-mail-checkbox',
    'favorite-mail-checkbox',
    'like-mail-checkbox',
    'edit-mail-checkbox'
  ];

  for(var i=0;i<targets.length;i++) {
    $("[name='" + targets[i] + "']").bootstrapSwitch({
      'size' : 'small',
      'onColor' : 'success'
    });
  }
});
