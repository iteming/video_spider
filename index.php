<?php
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
ini_set('display_errors', 'off');
error_reporting(E_ALL || ~E_NOTICE);
require 'src/video_spider.php';
$share_url = $_REQUEST['url'];
$id = $_REQUEST['id']; //微视 isee
$vid = $_REQUEST['vid']; //全民
$basai_id = $_REQUEST['data']; //巴塞电影
use Video_spider\Video;

$api = new Video;

// 正则匹配获取URL地址
$match_url = '/(http:\/\/|https:\/\/)((\w|=|\?|\.|\/|&|-)+)/';
preg_match_all($match_url, $share_url, $arr);
$url = $arr[0][0];

if (strpos($url, 'pipix') !== false) {
    $arr = $api->pipixia($url);
} elseif (strpos($url, 'douyin') !== false) {
    $arr = $api->douyin($url);
} elseif (strpos($url, 'huoshan') !== false) {
    $arr = $api->huoshan($url);
} elseif (strpos($url, 'h5.weishi') !== false) {
    $arr = $api->weishi($url);
} elseif (strpos($url, 'isee.weishi') !== false) {
    $arr = $api->weishi($id);
} elseif (strpos($url, 'weibo.com') !== false) {
    $arr = $api->weibo($url);
} elseif (strpos($url, 'oasis.weibo') !== false) {
    $arr = $api->lvzhou($url);
} elseif (strpos($url, 'zuiyou') !== false) {
    $arr = $api->zuiyou($url);
} elseif (strpos($url, 'bbq.bilibili') !== false) {
    $arr = $api->bbq($url);
} elseif (strpos($url, 'b23.tv') !== false) {
    $arr = $api->btv($url);
} elseif (strpos($url, 'kuaishou') !== false) {
    $arr = $api->kuaishou($url);
} elseif (strpos($url, 'quanmin') !== false) {
    $arr = $api->quanmin($vid);
} elseif (strpos($url, 'moviebase') !== false) {
    $arr = $api->basai($basai_id);
} elseif (strpos($url, 'hanyuhl') !== false) {
    $arr = $api->before($url);
} elseif (strpos($url, 'eyepetizer') !== false) {
    $arr = $api->kaiyan($url);
} elseif (strpos($url, 'immomo') !== false) {
    $arr = $api->momo($url);
} elseif (strpos($url, 'vuevideo') !== false) {
    $arr = $api->vuevlog($url);
} elseif (strpos($url, 'xiaokaxiu') !== false) {
    $arr = $api->xiaokaxiu($url);
} elseif (strpos($url, 'ippzone') !== false) {
    $arr = $api->pipigaoxiao($url);
} elseif (strpos($url, 'qq.com') !== false) {
    $arr = $api->quanminkge($url);
} else {
    $arr = array(
        'code' => 201,
        'msg' => '不支持您输入的链接'
    );
}
if (!empty($arr)) {
    $video_url = $arr['data']['url'];
    if ($video_url != null) {
        preg_match('/https:\/\/(.*)\?/', $video_url, $match_arr);
        if (count($match_arr) == 0) {
            $arr['data']['url'] = str_replace('http://', 'https://', $video_url);
        }
    }
    echo json_encode($arr, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
?>
