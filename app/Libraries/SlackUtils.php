<?php namespace Owl\Libraries;

use Illuminate\Contracts\Config\Repository as Config;

class SlackUtils
{
    /** enable */
    public $is_enabled = false;

    /** Slack webhook url */
    public $webhook_url = '';

    /** Bot icon url */
    public $icon_url = 'https://raw.githubusercontent.com/wiki/owl/owl/images/owl_webhook_logo.png';

    /** Bot user name */
    public $username = 'owl';

    public function __construct(Config $config)
    {
        $this->is_enabled  = $config->get('notification.slack.enabled');
        $this->webhook_url = $config->get('notification.slack.webhook_url');
    }

    public function postMessage($params)
    {
        if (! $this->is_enabled) {
            return false;
        }

        return $this->request($this->createOptions($this->formatParams($params)));
    }

    public function postCreateMessage($item, $user)
    {
        $params = [];
        $params['fallback']    = "新しい投稿がありました。( " . \Request::root() . '/items/' . $item->open_item_id . " )";
        $params['pretext']     = \Request::root() . '/items/' . $item->open_item_id;
        $params['author_name'] = $user->username;
        $params['author_link'] = \Request::root() . '/' . $user->username;
        $params['author_icon'] = \HTML::gravator($user->email, 16, 'mm', 'g', false);
        $params['title']       = $item->title;
        $params['title_link']  = \Request::root() . '/items/' . $item->open_item_id;
        $params['text']        = mb_strimwidth($item->body, 0, 200, "...");
        return $this->postMessage($params);
    }

    public function postEditMessage($item, $user)
    {
        $params = [];
        $params['fallback']    = "記事が編集されました。( " . \Request::root() . '/items/' . $item->open_item_id . " )";
        $params['pretext']     = \Request::root() . '/items/' . $item->open_item_id;
        $params['author_name'] = $user->username;
        $params['author_link'] = \Request::root() . '/' . $user->username;
        $params['author_icon'] = \HTML::gravator($user->email, 16, 'mm', 'g', false);
        $params['title']       = $item->title;
        $params['title_link']  = \Request::root() . '/items/' . $item->open_item_id;
        $params['text']        = mb_strimwidth($item->body, 0, 200, "...");
        return $this->postMessage($params);
    }

    protected function formatParams($params)
    {
        return [
            'url'  => $this->webhook_url,
            'body' => [
                'payload' => json_encode([
                    'username'    => $this->username,
                    'icon_url'    => $this->icon_url,
                    'attachments' => [[
                        'fallback'    => $params['fallback'] ?: '',
                        'author_name' => $params['author_name'] ?: '',
                        'author_link' => $params['author_link'] ?: '',
                        'author_icon' => $params['author_icon'] ?: '',
                        'title'       => $params['title'] ?: '',
                        'title_link'  => $params['title_link'] ?: '',
                        'text'        => $params['text'] ?: '',
                    ]]
                ]),
            ],
        ];
    }

    protected function createOptions($params)
    {
        return [
            CURLOPT_URL            => $params['url'],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $params['body'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
        ];
    }

    protected function request($options)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result      = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header      = substr($result, 0, $header_size);
        $result      = substr($result, $header_size);
        curl_close($ch);

        return [
            'Header' => $header,
            'Result' => $result,
        ];
    }
}
