<?php
/**
 * @package Video_spider
 * @author  iami233
 * @version 1.0.2
 * @link    https://github.com/5ime/Video_spider
 **/

namespace Video_spider;
class Video
{
    public function pipixia($url)
    {
        $loc = get_headers($url, true)['Location'];
        preg_match('/item\/(.*)\?/', $loc, $id);
        $arr = json_decode($this->curl('https://is.snssdk.com/bds/cell/detail/?cell_type=1&aid=1319&app_name=super&cell_id=' . $id[1]), true);
        $arr = array(
            'code' => 200,
            'data' => array(
                'author' => $arr['data']['data']['item'] ['author']['name'],
                'avatar' => $arr['data']['data']['item'] ['author']['avatar']['download_list'][0]['url'],
                'time' => $arr['data']['data']['display_time'],
                'title' => $arr['data']['data']['item']['content'],
                'cover' => $arr['data']['data']['item']['cover']['url_list'][0]['url'],
                'url' => $arr['data']['data']['item']['origin_video_download']['url_list'][0]['url']
            )
        );
        return $arr;
    }

    public function douyin($url)
    {
        $loc = get_headers($url, true)['Location'];
        preg_match('/video\/(.*)\/\?/', $loc, $id);

        // 关于这里的第三方接口问题 请查看 https://github.com/5ime/video_spider#faq
        $url = 'http://chik.cn:9002/';
        $data = json_encode(array('url' => 'https://www.douyin.com/aweme/v1/web/aweme/detail/?aweme_id=' . $id[1] . '&aid=1128&version_name=23.5.0&device_platform=android&os_version=2333','userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36'));
        $header = array('Content-Type: application/json');
        $url = json_decode($this->curl($url, $header, $data), true)['data']['url'];

        $msToken = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 107);
        $header = array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36', 'Referer: https://www.douyin.com/', 'Cookie: msToken='.$msToken.';odin_tt=324fb4ea4a89c0c05827e18a1ed9cf9bf8a17f7705fcc793fec935b637867e2a5a9b8168c885554d029919117a18ba69; ttwid=1%7CWBuxH_bhbuTENNtACXoesI5QHV2Dt9-vkMGVHSRRbgY%7C1677118712%7C1d87ba1ea2cdf05d80204aea2e1036451dae638e7765b8a4d59d87fa05dd39ff; bd_ticket_guard_client_data=eyJiZC10aWNrZXQtZ3VhcmQtdmVyc2lvbiI6MiwiYmQtdGlja2V0LWd1YXJkLWNsaWVudC1jc3IiOiItLS0tLUJFR0lOIENFUlRJRklDQVRFIFJFUVVFU1QtLS0tLVxyXG5NSUlCRFRDQnRRSUJBREFuTVFzd0NRWURWUVFHRXdKRFRqRVlNQllHQTFVRUF3d1BZbVJmZEdsamEyVjBYMmQxXHJcbllYSmtNRmt3RXdZSEtvWkl6ajBDQVFZSUtvWkl6ajBEQVFjRFFnQUVKUDZzbjNLRlFBNUROSEcyK2F4bXAwNG5cclxud1hBSTZDU1IyZW1sVUE5QTZ4aGQzbVlPUlI4NVRLZ2tXd1FJSmp3Nyszdnc0Z2NNRG5iOTRoS3MvSjFJc3FBc1xyXG5NQ29HQ1NxR1NJYjNEUUVKRGpFZE1Cc3dHUVlEVlIwUkJCSXdFSUlPZDNkM0xtUnZkWGxwYmk1amIyMHdDZ1lJXHJcbktvWkl6ajBFQXdJRFJ3QXdSQUlnVmJkWTI0c0RYS0c0S2h3WlBmOHpxVDRBU0ROamNUb2FFRi9MQnd2QS8xSUNcclxuSURiVmZCUk1PQVB5cWJkcytld1QwSDZqdDg1czZZTVNVZEo5Z2dmOWlmeTBcclxuLS0tLS1FTkQgQ0VSVElGSUNBVEUgUkVRVUVTVC0tLS0tXHJcbiJ9');
        $arr = json_decode($this->curl($url, $header), true);
        $video_url = $arr['aweme_detail']['video']['play_addr']['url_list'][0];

        if (empty($video_url)) {
            $arr = array('code' => 201, 'msg' => '解析失败');
            return $arr;
        }
        if ($arr['status_code']==0) {
            $arr = ['code' => 200,
                'msg' => '解析成功',
                'data' => [
                    'author' => $arr['aweme_detail']['author']['nickname'],
                    'uid' => $arr['aweme_detail']['author']['unique_id'],
                    'avatar' => $arr['aweme_detail']['music']['avatar_large']['url_list'][0],
                    'like' => $arr['aweme_detail']['statistics']['digg_count'],
                    'time' => $arr['aweme_detail']["create_time"],
                    'title' => $arr['aweme_detail']['desc'],
                    'cover' => $arr['aweme_detail']['video']['origin_cover']['url_list'][0],
                    'url' => $arr['aweme_detail']['video']['play_addr']['url_list'][0],
                    'musicurl' => $arr['aweme_detail']['music']['play_url']['url_list'][0],
                    'music' => [
                        'author' => $arr['aweme_detail']['music']['author'],
                        'avatar' => $arr['aweme_detail']['music']['cover_large']['url_list'][0],
                        'url' => $arr['aweme_detail']['music']['play_url']['url_list'][0],
                    ]
                ]
            ];
            return $arr;
        }
        return $arr;
    }


    public function douyin_web($url)
    {
        preg_match('/video\/(.*)/', $url, $id);
        $url = 'http://chik.cn:9002/';
        $data = json_encode(array('url' => 'https://www.douyin.com/aweme/v1/web/aweme/detail/?aweme_id=' . $id[1] . '&aid=1128&version_name=23.5.0&device_platform=android&os_version=2333','userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36'));
        $header = array('Content-Type: application/json');
        $url = json_decode($this->curl($url, $header, $data), true)['data']['url'];

        $msToken = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 107);
        $header = array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36', 'Referer: https://www.douyin.com/', 'Cookie: msToken='.$msToken.';odin_tt=324fb4ea4a89c0c05827e18a1ed9cf9bf8a17f7705fcc793fec935b637867e2a5a9b8168c885554d029919117a18ba69; ttwid=1%7CWBuxH_bhbuTENNtACXoesI5QHV2Dt9-vkMGVHSRRbgY%7C1677118712%7C1d87ba1ea2cdf05d80204aea2e1036451dae638e7765b8a4d59d87fa05dd39ff; bd_ticket_guard_client_data=eyJiZC10aWNrZXQtZ3VhcmQtdmVyc2lvbiI6MiwiYmQtdGlja2V0LWd1YXJkLWNsaWVudC1jc3IiOiItLS0tLUJFR0lOIENFUlRJRklDQVRFIFJFUVVFU1QtLS0tLVxyXG5NSUlCRFRDQnRRSUJBREFuTVFzd0NRWURWUVFHRXdKRFRqRVlNQllHQTFVRUF3d1BZbVJmZEdsamEyVjBYMmQxXHJcbllYSmtNRmt3RXdZSEtvWkl6ajBDQVFZSUtvWkl6ajBEQVFjRFFnQUVKUDZzbjNLRlFBNUROSEcyK2F4bXAwNG5cclxud1hBSTZDU1IyZW1sVUE5QTZ4aGQzbVlPUlI4NVRLZ2tXd1FJSmp3Nyszdnc0Z2NNRG5iOTRoS3MvSjFJc3FBc1xyXG5NQ29HQ1NxR1NJYjNEUUVKRGpFZE1Cc3dHUVlEVlIwUkJCSXdFSUlPZDNkM0xtUnZkWGxwYmk1amIyMHdDZ1lJXHJcbktvWkl6ajBFQXdJRFJ3QXdSQUlnVmJkWTI0c0RYS0c0S2h3WlBmOHpxVDRBU0ROamNUb2FFRi9MQnd2QS8xSUNcclxuSURiVmZCUk1PQVB5cWJkcytld1QwSDZqdDg1czZZTVNVZEo5Z2dmOWlmeTBcclxuLS0tLS1FTkQgQ0VSVElGSUNBVEUgUkVRVUVTVC0tLS0tXHJcbiJ9');
        $arr = json_decode($this->curl($url, $header), true);
        $video_url = $arr['aweme_detail']['video']['play_addr']['url_list'][0];

        if (empty($video_url)) {
            $arr = array('code' => 201, 'msg' => '解析失败');
            return $arr;
        }
        if ($arr['status_code']==0) {
            $arr = ['code' => 200,
                'msg' => '解析成功',
                'data' => [
                    'author' => $arr['aweme_detail']['author']['nickname'],
                    'uid' => $arr['aweme_detail']['author']['unique_id'],
                    'avatar' => $arr['aweme_detail']['music']['avatar_large']['url_list'][0],
                    'like' => $arr['aweme_detail']['statistics']['digg_count'],
                    'time' => $arr['aweme_detail']["create_time"],
                    'title' => $arr['aweme_detail']['desc'],
                    'cover' => $arr['aweme_detail']['video']['origin_cover']['url_list'][0],
                    'url' => $arr['aweme_detail']['video']['play_addr']['url_list'][0],
                    'musicurl' => $arr['aweme_detail']['music']['play_url']['url_list'][0],
                    'music' => [
                        'author' => $arr['aweme_detail']['music']['author'],
                        'avatar' => $arr['aweme_detail']['music']['cover_large']['url_list'][0],
                        'url' => $arr['aweme_detail']['music']['play_url']['url_list'][0],
                    ]
                ]
            ];
            return $arr;
        }
        return $arr;
    }

    public function huoshan($url)
    {
        $loc = get_headers($url, true)['Location'];
        preg_match('/item_id=(.*)&tag/', $loc, $id);
        $arr = json_decode($this->curl('https://share.huoshan.com/api/item/info?item_id=' . $id[1]), true);
        $url = $arr['data']['item_info']['url'];
        preg_match('/video_id=(.*)&line/', $url, $id);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'cover' => $arr["data"]["item_info"]["cover"],
                'url' => 'https://api-hl.huoshan.com/hotsoon/item/video/_playback/?video_id=' . $id[1]
            )
        );
        return $arr;
    }

    public function weishi($url)
    {
        preg_match('/feed\/(.*)\b/', $url, $id);
        if (strpos($url, 'h5.weishi') != false) {
            $arr = json_decode($this->curl('https://h5.weishi.qq.com/webapp/json/weishi/WSH5GetPlayPage?feedid=' . $id[1]), true);
        } else {
            $arr = json_decode($this->curl('https://h5.weishi.qq.com/webapp/json/weishi/WSH5GetPlayPage?feedid=' . $url), true);
        }
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $arr['data']['feeds'][0]['poster']['nick'],
                'avatar' => $arr['data']['feeds'][0]['poster']['avatar'],
                'time' => $arr['data']['feeds'][0]['poster']['createtime'],
                'title' => $arr['data']['feeds'][0]['feed_desc_withat'],
                'cover' => $arr['data']['feeds'][0]['images'][0]['url'],
                'url' => $arr['data']['feeds'][0]['video_url'],
            )
        );
        return $arr;
    }

    public function weibo($url)
    {
        if (strpos($url, 'show?fid=') != false) {
            preg_match('/fid=(.*)/', $url, $id);
            $arr = json_decode($this->curl('https://video.h5.weibo.cn/s/video/object?object_id=' . $id[1]), true);
        } else {
            preg_match('/\d+\:\d+/', $url, $id);
            $arr = json_decode($this->curl('https://video.h5.weibo.cn/s/video/object?object_id=' . $id[0]), true);
        }
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $arr['data']['object']['author']['screen_name'],
                'avatar' => $arr['data']['object']['author']['profile_image_url'],
                'time' => $arr['data']['object']['created_at'],
                'title' => $arr['data']['object']['summary'],
                'cover' => $arr['data']['object']['image']['url'],
                'url' => $arr['data']['object']['stream']['hd_url']
            )
        );
        return $arr;
    }

    public function lvzhou($url)
    {
        $text = $this->curl($url);
        preg_match('/<div class=\"text\">(.*)<\/div>/', $text, $video_title);
        preg_match('/<div style=\"background-image:url\((.*)\)/', $text, $video_cover);
        preg_match('/<video src=\"([^\"]*)\"/', $text, $video_url);
        preg_match('/<div class=\"nickname\">(.*)<\/div>/', $text, $video_author);
        preg_match('/<a class=\"avatar\"><img src=\"(.*)\?/', $text, $video_author_img);
        preg_match('/<div class=\"like-count\">(.*)次点赞<\/div>/', $text, $video_like);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $video_author[1],
                'avatar' => str_replace('1080.180', '1080.680', $video_author_img)[1],
                'like' => $video_like[1],
                'title' => $video_title[1],
                'cover' => $video_cover[1],
                'url' => $video_url[1],
            )
        );
        return $arr;
    }

    public function zuiyou($url)
    {
        $text = $this->curl($url);
        preg_match('/:<\/span>(.*?)<\/div><\/div><div class=\"ImageBoxII\">/', $text, $video_title);
        preg_match('/<img alt=\"\" src=\"(.*?)\/id\/(.*?)\/sz/', $text, $video_cover);
        preg_match('/' . $video_cover[2] . ',\"(.*)url\":\"(.*)\",\"prior/', $text, $url);
        $video_url = str_replace('\\', '/', str_replace('u002F', '', $url[2]));
        preg_match('/<span class=\"SharePostCard__name\">(.*?)<\/span>/', $text, $video_author);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $video_author[1],
                'title' => $video_title[1],
                'cover' => 'https://file.izuiyou.com/img/png/id/' . $video_cover[2] . '/sz/600',
                'url' => $video_url,
            )
        );
        return $arr;
    }

    public function btv($url)
    {
        // 添加bilibili的解析，暂时找不到无水印的解析地址，2021.03.26
        if (strlen($url) >= 16)
            $base_url = $url;
        else
            $base_url = 'https://m.bilibili.com/video/' . $url;

        $content = $this->curl($base_url,[], true);
        preg_match('/var options = (.*?) {10}var player =/', str_replace(array("\r\n", "\r", "\n"), '', $content), $json_content);
        preg_match('/\'bvid\':\'(.*?)\',/', str_replace('"', '\'', str_replace(' ', '', $json_content[1])), $arr_bvid);
        preg_match('/\'readyDuration\':(.*?),/', str_replace('"', '\'', str_replace(' ', '', $json_content[1])), $arr_readyDuration);
        preg_match('/\'readyPoster\':\'(.*?)\',/', str_replace('"', '\'', str_replace(' ', '', $json_content[1])), $arr_readyPoster);
        preg_match('/\'readyVideoUrl\':\'(.*?)\',/', str_replace('"', '\'', str_replace(' ', '', $json_content[1])), $arr_readyVideoUrl);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => '',
                'avatar' => '',
                'time' => $arr_readyDuration[1],
                'like' => '',
                'title' => $arr_bvid[1],
                'cover' => $arr_readyPoster[1],
                'url' => $arr_readyVideoUrl[1],
            )
        );
        return $arr;
    }

    public function bbq($url)
    {
        preg_match('/id=(.*)\b/', $url, $id);
        $arr = json_decode($this->curl('https://bbq.bilibili.com/bbq/app-bbq/sv/detail?svid=' . $id[1]), true);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $arr['data']['user_info']['uname'],
                'avatar' => $arr['data']['user_info']['face'],
                'time' => $arr['data']['pubtime'],
                'like' => $arr['data']['like'],
                'title' => $arr['data']['title'],
                'cover' => $arr['data']['cover_url'],
                'url' => $arr['data']['play']['file_info'][0]['url'],
            )
        );
        return $arr;
    }

    public function kuaishou($url)
    {
        $loc = get_headers($url, true)["Location"][0];
        $text = $this->curl($loc);
        preg_match('/{\"title\":\"(.*?)\",\"desc/', $text, $video_title);
        preg_match('/poster=\"(.*?)\"/', $text, $video_cover);
        preg_match('/srcNoMark\":\"(.*?)\"}/', $text, $video_url);
        preg_match('/<div class=\"auth-name\">(.*?)<\/div>/', $text, $video_author);
        preg_match('/<div class=\"auth-avatar\" style=\"background-image:url\((.*?)\)/', $text, $video_avatar);
        preg_match('/timestamp\":(.*?),\"/', $text, $video_time);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $video_author[1],
                'avatar' => $video_avatar[1],
                'time' => $video_time[1],
                "title" => $video_title[1],
                "cover" => $video_cover[1],
                "url" => $video_url[1],
            )
        );
        return $arr;
    }

    public function quanmin($id)
    {
        $arr = json_decode($this->curl('https://quanmin.hao222.com/wise/growth/api/sv/immerse?source=share-h5&pd=qm_share_mvideo&vid=' . $id . '&_format=json'), true);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $arr["data"]["author"]['name'],
                'avatar' => $arr["data"]["author"]["icon"],
                "title" => $arr["data"]["meta"]["title"],
                "cover" => $arr["data"]["meta"]["image"],
                "url" => $arr["data"]["meta"]["video_info"]["clarityUrl"][0]['url']
            )
        );
        return $arr;
    }

    public function basai($id)
    {
        $arr = json_decode($this->curl('http://www.moviebase.cn/uread/api/m/video/' . $id . '?actionkey=300303'), true);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'time' => $arr[0]['data']['createDate'],
                'title' => $arr[0]['data']['title'],
                "cover" => $arr[0]['data']['coverUrl'],
                "url" => $arr[0]['data']['videoUrl']
            )
        );
        return $arr;
    }

    public function before($url)
    {
        preg_match('/detail\/(.*)\?/', $url, $id);
        $arr = json_decode($this->curl('https://hlg.xiatou.com/h5/feed/detail?id=' . $id[1]), true);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $arr['data'][0]['author']['nickName'],
                'avatar' => $arr['data'][0]['author']['avatar']['url'],
                'like' => $arr['data'][0]['diggCount'],
                'time' => $arr['recTimeStamp'],
                'title' => $arr['data'][0]['title'],
                "cover" => $arr['data'][0]['staticCover'][0]['url'],
                "url" => $arr['data'][0]['mediaInfoList'][0]['videoInfo']['url']
            )
        );
        return $arr;
    }

    public function kaiyan($url)
    {
        preg_match('/\?vid=(.*)\b/', $url, $id);
        $arr = json_decode($this->curl('https://baobab.kaiyanapp.com/api/v1/video/' . $id[1] . '?f=web'), true);
        $video = 'https://baobab.kaiyanapp.com/api/v1/playUrl?vid=' . $id[1] . '&resourceType=video&editionType=default&source=aliyun&playUrlType=url_oss&ptl=true';
        $video_url = get_headers($video, true)["Location"];
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'title' => $arr['title'],
                "cover" => $arr['coverForFeed'],
                "url" => $video_url
            )
        );
        return $arr;
    }

    public function momo($url)
    {
        preg_match('/new-share-v2\/(.*)\.html/', $url, $id);
        $post_data = array("feedids" => $id[1],);
        $arr = json_decode($this->post_curl('https://m.immomo.com/inc/microvideo/share/profiles', $post_data), true);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $arr['data']['list'][0]['user']['name'],
                'avatar' => $arr['data']['list'][0]['user']['img'],
                'uid' => $arr['data']['list'][0]['user']['momoid'],
                'sex' => $arr['data']['list'][0]['user']['sex'],
                'age' => $arr['data']['list'][0]['user']['age'],
                'city' => $arr['data']['list'][0]['video']['city'],
                'like' => $arr['data']['list'][0]['video']['like_cnt'],
                'title' => $arr['data']['list'][0]['content'],
                "cover" => $arr['data']['list'][0]['video']['cover']['l'],
                "url" => $arr['data']['list'][0]['video']['video_url']
            )
        );
        return $arr;
    }

    public function vuevlog($url)
    {
        $text = $this->curl($url);
        preg_match('/<title>(.*?)<\/title>/', $text, $video_title);
        preg_match('/<meta name=\"twitter:image\" content=\"(.*?)\">/', $text, $video_cover);
        preg_match('/<meta property=\"og:video:url\" content=\"(.*?)\">/', $text, $video_url);
        preg_match('/<div class=\"infoItem name\">(.*?)<\/div>/', $text, $video_author);
        preg_match('/<div class="avatarContainer"><img src="(.*?)\"/', $text, $video_avatar);
        preg_match('/<div class=\"likeTitle\">(.*) friends/', $text, $video_like);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $video_author[1],
                'avatar' => $video_avatar[1],
                'like' => $video_like[1],
                'title' => $video_title[1],
                "cover" => $video_cover[1],
                "url" => $video_url[1],
            )
        );
        return $arr;
    }

    public function xiaokaxiu($url)
    {
        preg_match('/id=(.*)\b/', $url, $id);
        $sign = md5('S14OnTD#Qvdv3L=3vm&time=' . time());
        $arr = json_decode($this->curl('https://appapi.xiaokaxiu.com/api/v1/web/share/video/' . $id[1] . '?time=' . time(), ["x-sign : $sign"]), true);
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'author' => $arr['data']['video']['user']['nickname'],
                'avatar' => $arr['data']['video']['user']['avatar'],
                'like' => $arr['data']['video']['likedCount'],
                'time' => $arr['data']['video']['createdAt'],
                'title' => $arr['data']['video']['title'],
                'cover' => $arr['data']['video']['cover'],
                'url' => $arr['data']['video']['url'][0]
            )
        );
        return $arr;
    }

    public function pipigaoxiao($url)
    {
        preg_match('/post\/(.*)/', $url, $id);
        $arr = json_decode($this->pipigaoxiao_curl($id[1]), true);
        $id = $arr["data"]["post"]["imgs"][0]["id"];
        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'data' => array(
                'title' => $arr["data"]["post"]["content"],
                'cover' => 'https://file.ippzone.com/img/view/id/' . $arr["data"]["post"]["imgs"][0]["id"],
                'url' => $arr["data"]["post"]["videos"]["$id"]["url"]
            )
        );
        return $arr;
    }

    public function quanminkge($url)
    {
        preg_match('/\?s=(.*)/', $url, $id);
        $text = $this->curl('https://kg.qq.com/node/play?s=' . $id[1]);
        preg_match('/<title>(.*?)-(.*?)-/', $text, $video_title);
        preg_match('/cover\":\"(.*?)\"/', $text, $video_cover);
        preg_match('/playurl_video\":\"(.*?)\"/', $text, $video_url);
        preg_match('/{\"activity_id\":0\,\"avatar\":\"(.*?)\"/', $text, $video_avatar);
        preg_match('/<p class=\"singer_more__time\">(.*?)<\/p>/', $text, $video_time);

        $arr = array(
            'code' => 200,
            'msg' => '解析成功',
            'author' => $video_title[1],
            'avatar' => $video_avatar[1],
            'time' => $video_time[1],
            'data' => array(
                'title' => $video_title[2],
                'cover' => $video_cover[1],
                'url' => $video_url[1],
            )
        );
        return $arr;
    }

    private function curl($url, $header = array(), $data = array()) {
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($con, CURLOPT_AUTOREFERER, 1);
        if (isset($header)) {
            curl_setopt($con, CURLOPT_HTTPHEADER, $header);
        }
        if (isset($data)) {
            curl_setopt($con, CURLOPT_POST, true);
            curl_setopt($con, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($con, CURLOPT_TIMEOUT, 5000);
        $result = curl_exec($con);
        return $result;
    }

    private function post_curl($url, $post_data)
    {
        $postdata = http_build_query($post_data);
        $options = array('http' => array(
            'method' => 'POST',
            'content' => $postdata,
        ));
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        return $result;
    }

    private function pipigaoxiao_curl($id)
    {
        $post_data = "{\"pid\":" . $id . ",\"type\":\"post\",\"mid\":null}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://share.ippzone.com/ppapi/share/fetch_content");
        curl_setopt($ch, CURLOPT_REFERER, "http://share.ippzone.com/ppapi/share/fetch_content");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    private function get_href_url($content)
    {
        preg_match('/href="(.*?)">Found/', $content, $matches);
        $res = $matches[1];
        return $res;
    }

    private function get_short_url($video_url)
    {
        $url = "https://api.chik.cn/shortUrlGenerator?sourceUrl=" . $video_url . "&length=8&hours=24";
        $response = $this->curl($url);
        $retrun_url = "https://api.chik.cn/r?u=" . json_decode($response, true)['shortChar'];
        return $retrun_url;
    }

    public function get_oss_url($video_url)
    {
        $url = "https://api.chik.cn/v1/oss/uploadUrl?sourceUrl=" . $video_url;
        $response = $this->curl($url);
        $retrun_url = json_decode($response, true)['data'];
        return $retrun_url;
    }
}
