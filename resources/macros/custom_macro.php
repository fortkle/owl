<?php

\HTML::macro('cached_asset', function($path)
{
    $realPath = public_path($path);

    if ( ! file_exists($realPath)) {
        throw new LogicException("File not found at [{$realPath}]");
    }

    $timestamp = filemtime($realPath);
    $path  .= '?' . $timestamp;

    return asset($path);
});

\HTML::macro('gravator', function($email, $s = 80, $d = 'mm', $r = 'g', $img = true, $atts = array())
{
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
});

\HTML::macro('tags', function($array)
{
    $tag_lists = "";
    foreach($array as $tag){
        if($tag === end($array)) {
            $tag_lists .= $tag["name"];
            break;
        }
        $tag_lists .= $tag["name"] . ",";
    }
    return $tag_lists;
});

\HTML::macro('show_tags', function ($tags)
{
    $tag_lists= [];

    foreach ($tags as $tag) {
        $tag_name     = $tag['name'];
        $tag_page_url = route('tags.show', ['tags' => $tag_name]);

        $tag_lists[] = '<span class="tag-label"><a href="'. $tag_page_url.'">'. e($tag_name) .'</a></span>';
    }

    return implode(' ', $tag_lists);
});

HTML::macro('show_users', function($users)
{
    $user_lists = [];

    foreach ($users as $user) {
        $image         = HTML::gravator($user["email"], 20);
        $username      = $user['username'];
        $user_page_url = route('user.profile', compact('username'));

        $user_lists[] = $image . ' <a href="'. $user_page_url.'">'. e($username) .'</a>';
    }

    return implode('　', $user_lists);
});

HTML::macro('markdown', function($str)
{
    $parser = new Owl\Libraries\CustomMarkdown;
    $parser->enableNewlines = true;
    return $parser->parse($str);
});

HTML::macro('diff', function($from, $to)
{
    $from = mb_convert_encoding($from, 'HTML-ENTITIES', 'UTF-8');
    $to = mb_convert_encoding($to, 'HTML-ENTITIES', 'UTF-8');

    $granularity = new cogpowered\FineDiff\Granularity\Word;
    $diff        = new cogpowered\FineDiff\Diff($granularity);
    $result = htmlspecialchars_decode($diff->render($from, $to));
    return nl2br($result);
});

HTML::macro('date_replace', function($str)
{
    $str = str_replace("%{Year}",date('Y'), $str);
    $str = str_replace("%{month}",date('m'), $str);
    $str = str_replace("%{day}",date('d'), $str);
    return $str;
});



