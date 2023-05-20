<?php

namespace classes;

abstract class Controller
{
    abstract public function show();

    protected function userMustBeLogged() {
        if(!User::isLogged()) {
            Router::redirect("Login");
            exit();
        }
    }

    protected function isPOST(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
}