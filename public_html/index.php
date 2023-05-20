<?php
require_once 'config.php';

use classes\Session;
use classes\Router;

Session::start();
Router::run();
