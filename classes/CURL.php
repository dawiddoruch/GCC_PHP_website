<?php

namespace classes;

class CURL
{
    static public function getJSON($url, $cacheFile = null) {

        $json = '';
        $cacheFileExists = false;

        $filePath = '';

        if($cacheFile != null) {
            $filePath = __DIR__.'/../cache/'.$cacheFile.'.cache';
            if(file_exists($filePath)) {
                $cacheFileExists = true;
                $json = file_get_contents($filePath);
                Message::info("Contents red from cache");
            }
        }

        if(!$cacheFileExists) {
            $json = self::curlExecute($url);
            Message::info("Contents red from NASA API");
            if($json != null && $cacheFile != null) {
                file_put_contents($filePath, $json);
            }
        }

        return json_decode($json);
    }

    static private function curlExecute($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json = curl_exec($ch);

        if(curl_error($ch)) {
            Message::error("Can not read from ".$url);
            return null;
        }

        curl_close($ch);
        return $json;
    }
}