<?php

namespace controllers;

use classes\User;
use classes\Router;

class Home extends \classes\Controller
{
    public function show()
    {
        $this->userMustBeLogged();
        Router::redirect("Search");
    }
}