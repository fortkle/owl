<?php namespace Owl\Http\ViewComposers;

/**
 * @copyright (c) owl
 */

use Illuminate\View\View;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Config\Repository as Config;
use Owl\Services\MailNotifyService;

/**
 * Class MailNotifySettingComposer
 *
 * @package Owl\Http\ViewComposers
 */
class MailNotifySettingComposer
{
    /** @var AuthManager */
    protected $auth;

    /** @var bool */
    protected $notifyEnable;

    /** @var MailNotifyService */
    protected $mailNotifyService;

    /**
     * @param AuthManager        $auth
     * @param Config             $config
     * @param MailNotifyService  $mailNotifyService
     */
    public function __construct(
        AuthManager       $auth,
        Config            $config,
        MailNotifyService $mailNotifyService
    ) {
        $this->auth = $auth;
        $this->notifyEnable =
            $config->get('notification.enabled') && $config->get('mail.mail_enable');
        $this->mailNotifyService = $mailNotifyService;
    }

    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $view->getFactory()->inject('mail_notify_setting', $this->renderHtml());
        $view->getFactory()->inject('mail_notify_setting_addJs', $this->renderJs());
        $view->getFactory()->inject('mail_notify_setting_addCss', $this->renderCss());
    }

    /**
     * Configに合わせてメール通知HTMLコンポーネントを返す
     *
     * @return View | null
     */
    protected function renderHtml()
    {
        if ($this->notifyEnable) {
            $notifyFlags = $this->mailNotifyService->getSettings(
                $this->auth->user()->getAuthIdentifier()
            );
            return view('user.edit._mail-notify', compact('notifyFlags'))->render();
        }

        return null;
    }

    /**
     * メール通知コンポーネント用JS読み込み用Viewを返す
     *
     * @return View | null
     */
    protected function renderJs()
    {
        if ($this->notifyEnable) {
            return view('user.edit._mail-notify-addJs')->render();
        }

        return null;
    }

    /**
     * メール通知コンポーネント用CSS読み込み用Viewを返す
     *
     * @return View | null
     */
    protected function renderCss()
    {
        if ($this->notifyEnable) {
            return view('user.edit._mail-notify-addCss')->render();
        }

        return null;
    }
}
