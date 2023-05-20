<?php

namespace classes;

class Debug
{
    private static bool $debug = false;

    public static function var_dump($data) {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
    }

    public static function print_r($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    public static function info($message, $override_debug_config = false) {
        if(!self::$debug && !$override_debug_config) return;
        echo "<pre>";
        echo $message;
        echo "</pre>";

    }
}