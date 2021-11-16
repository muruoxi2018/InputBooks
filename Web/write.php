<?php
header('Content-type:application/json;charset=utf-8');
/*
 * Copyright (C) www.muruoxi.com
 */

!defined('DEBUG') and define('DEBUG', 1);   // 0: 线上模式; 1: 调试模式;
define('APP_PATH', dirname(__FILE__) . '/');  // __DIR__
!defined('XIUNOPHP_PATH') and define('XIUNOPHP_PATH', APP_PATH . 'xiunophp/');


$conf = (@include APP_PATH . 'conf.php') or exit('<script>window.location="https://www.muruoxi.com/"</script>');

if (DEBUG > 0) {
    include XIUNOPHP_PATH . 'xiunophp.php';
} else {
    include XIUNOPHP_PATH . 'xiunophp.min.php';
}

// 转换为绝对路径，防止被包含时出错。
substr($conf['log_path'], 0, 2) == './' and $conf['log_path'] = APP_PATH . $conf['log_path'];
substr($conf['tmp_path'], 0, 2) == './' and $conf['tmp_path'] = APP_PATH . $conf['tmp_path'];
substr($conf['upload_path'], 0, 2) == './' and $conf['upload_path'] = APP_PATH . $conf['upload_path'];

$_SERVER['conf'] = $conf;

//测试数据库连接 / try to connect database
// db_connect() or exit($errstr);
// $_SERVER['db'] = $db = db_new($conf['db']);
// 此处可能报错
// $r = db_connect($db);
// print_r($r);

// 下面开始处理事物
// -1 意料外的错误
// 0 一切正常
// 1+ 各种识别中的状态

/**
 * 获取301跳转地址
 */
/*function getrealurl($url)
{
    $header = get_headers($url, 1);

    //print_r($header);

    if (strpos($header[0], '301') || strpos($header[0], '302')) {
        if (is_array($header['Location'])) {
            return $header['Location'][count($header['Location']) - 1];
        } else {
            return $header['Location'];
        }
    } else {
        return $url;
    }
}*/


//$url = getrealurl("https://book.douban.com/isbn/9787516408476/");

try {
    $contents = https_get("https://book.douban.com/isbn/" . param('isbn'));
    preg_match('/ISBN:<\/span> (.*?)<br\/>/', $contents, $matches);
    $isbn = $matches[1];
    if ($isbn != param('isbn')) throw new PDOException("书籍不匹配");
    preg_match('/<span property=\"v:itemreviewed\">(.*?)<\/span>/', $contents, $matches);
    $title = $matches[1];
    preg_match('/<a class=\"\" href=\"\/search\/(.*?)\">(.*?)<\/a>/', $contents, $matches);
    $author = $matches[2];
    preg_match('/出版社:<\/span> (.*?)<br\/>/', $contents, $matches);
    $press = $matches[1];
    preg_match('/出版年:<\/span> (.*?)<br\/>/', $contents, $matches);
    $year = $matches[1];
    preg_match('/页数:<\/span> (.*?)<br\/>/', $contents, $matches);
    $page = $matches[1];
    preg_match('/定价:<\/span> (.*?)<br\/>/', $contents, $matches);
    $money = $matches[1];
    preg_match('/装帧:<\/span> (.*?)<br\/>/', $contents, $matches);
    $binding = $matches[1];
    $message = [
        'biaoshi'=>param('biaoshi'),
        'ISBN' => $isbn,
        'title' => $title,
        'year' => $year,
        'author' => $author,
        'press' => $press,
        'page' => $page,
        'money' => $money,
        'binding' => $binding
    ];
    db_insert('books',$message);
    xn_message('success', $message);
} catch (PDOException $e) {
    xn_message('error', $e->getMessage());
}
