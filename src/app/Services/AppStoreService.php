<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\DomCrawler\Crawler;

class AppStoreService
{
    function docLoadUrl($link) {

        $content = curl_get($link);

        $doc = new Crawler();
        $doc->addHtmlContent($content, 'UTF-8');
        return $doc;
    }

    public function getAppInfo($appid) {
        $os = strpos($appid, '.') === false ? 'ios' : 'android';

        $rKey = "asoapp.appid.$os.$appid";
        $appData = Cache::get($rKey);
        if (!$appData) {
            if ($os === 'ios') {
                try {
                    $res = curl_get_json('https://itunes.apple.com/lookup?id=' . $appid);

                    if ($res['resultCount'] > 0) {
                        $appData = [
                            'os' => 'ios',
                            'name' => $res['results'][0]['trackName'],
                            'icon' => $this->downloadIcons($res['results'][0]['artworkUrl100']),
                            'storeUrl' => 'https://itunes.apple.com/us/app/apple-store/id' . $appid
                        ];

                        Cache::put($rKey, serialize($appData), 86400);
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage() ."\n";
                    echo $e->getTraceAsString() . "\n";
                    return false;
                }

            } else {
                // https://play.google.com/store/apps/details?id=com.omipharma.userapp
                $doc = $this->docLoadUrl('https://play.google.com/store/apps/details?id=' . $appid);

                try {
                    $title = $doc->filter('title')->text();

                    $images = $doc->filter('img')->each(function(Crawler $dom) {
                        $src = $dom->attr('src');
                        if (strpos($src, 'googleusercontent.com') !== false) {
                            return $src;
                        }
                        return null;
                    });
                    $images = array_values(array_filter($images));

                    if (count($images) ===0) {
                        return false;
                    }

                    $icon = $images[0];
                    if (!$icon) {
                        return false;
                    }


                    $appData = [
                        'os' => 'android',
                        'name' => $title,
                        'icon' => $icon,
                        'storeUrl' => 'https://play.google.com/store/apps/details?id=' .$appid,
                        'icon_original' => $icon
                    ];

                    Cache::put($rKey, serialize($appData), 86400);
                } catch (\Exception $e) {
                    echo $e->getMessage() . "<br>";
                    echo $e->getTraceAsString();
                    return false;
                }

            }
        } else {
            $appData = unserialize($appData);
        }

        return $appData;
    }

    private function downloadIcons($icon) {
        $y = date('Y');
        $m = date('m');
        $hash = md5($icon);
        $ext = 'jpg';

        $path = public_path("/images/icons/$y/$m");
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if (!file_exists($path . "/$hash.$ext")) {
            file_put_contents( $path . "/$hash.$ext" , file_get_contents_curl($icon));
        }


        return "/images/icons/$y/$m/$hash.$ext";
    }

}
