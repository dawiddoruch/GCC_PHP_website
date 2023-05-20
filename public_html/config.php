<?php

// secret.php is ignored in git.
// It includes my DB pass and API key
require_once 'secret.php';

const db_host = 'localhost';
const db_user = 'dawiddor3_nasa';
const db_name = 'dawiddor3_nasa';
const db_pass = secret_dbpass;
const url = "https://itismoto.com/";

const api_key = secret_nasa_api;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class Loader {
    static function classExists($class) {

    }

    static function autoload($class) {
        // echo "AAA: ".$class."<br>";
        include __DIR__.'/../'.str_replace('\\', '/', $class).'.php';
    }
}

spl_autoload_register("Loader::autoload");