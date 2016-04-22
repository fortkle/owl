<?php namespace Owl\Http\Controllers;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Owl\Http\Controllers\Controller;
use Owl\Services\MailNotifyService;

/**
 * Class MailNotifyjController
 * メール通知設定用コントローラークラス
 *
 * @package Owl\Http\Controllers
 */
class MailNotifyController extends Controller
{
    /**
     * 設定を更新
     *
     * @param Request            $request
     * @param AuthManager        $auth
     * @param MailNotifyService  $mailNotifyService
     *
     * @return \Illuminate\Http\Response
     */
    public function update(
        Request           $request,
        AuthManager       $auth,
        MailNotifyService $mailNotifyService
    ) {
        $result = $mailNotifyService->updateSetting(
            $auth->user()->getAuthIdentifier(), $request->get('type'), $request('flag')
        );

        return response()->json(['result' => $result]);
    }
}
