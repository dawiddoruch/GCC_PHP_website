<?php

namespace controllers;

use classes\Debug;
use classes\Message;
use classes\User;
use classes\Router;

class Login extends \classes\Controller
{
    /**
     * Login form
     */
    public function show()
    {
        $data = array();
        if($this->isPOST()) {
            if($this->tryLoggingIn()) {
                Message::info("User logged in");
                Router::redirect("Search");
            }
            $data['login'] = filter_input(INPUT_POST, "login");
        }

        $view = new \classes\View();
        $view->load("login", $data);
    }

    /**
     * Register form
     */
    public function register()
    {
        $data = array();
        if($this->isPOST()) {
            if($this->tryRegistering()) {
                Message::info("User registered. You can now sign in.");
                Router::redirect("Login");
            }
            $data['login'] = filter_input(INPUT_POST, "login");
        }

        $view = new \classes\View();
        $view->load("register", $data);
    }

    /**
     * Logout and redirect to home page
     */
    public function logout() {
        User::logout();
        Message::info("User logged out.");
        Router::redirect();
    }

    /**
     * Try logging in
     * @return bool
     */
    private function tryLoggingIn(): bool
    {
        $error = false;
        $login      = filter_input(INPUT_POST, "login", FILTER_VALIDATE_EMAIL);
        $password   = filter_input(INPUT_POST, "password");

        if($login === false) {
            Message::warning("Please type in valid email address as  your login.");
            $error = true;
        }

        if($password === false) {
            Message::warning("Please type in your password.");
            $error = true;
        }

        if($error) return false;

        return User::login($login, $password);
    }

    /**
     * Try registering
     * @return bool
     */
    private function tryRegistering(): bool
    {
        $error = false;
        $login      = filter_input(INPUT_POST, "login", FILTER_VALIDATE_EMAIL);
        $password   = filter_input(INPUT_POST, "password");
        $repeat     = filter_input(INPUT_POST, "repeat");

        if($login === false) {
            Message::warning("Please type in valid email address as your login.");
            $error = true;
        }

        if($password === false) {
            Message::warning("Please type in your password.");
            $error = true;
        }

        if($repeat === false) {
            Message::warning("Please confirm password.");
            $error = true;
        }

        if(strlen($password) < 8) {
            Message::warning("Password must be at least 8 characters.");
            $error = true;
        }

        if($password != $repeat) {
            Message::warning("Passwords do not match.");
            $error = true;
        }

        if($error) return false;

        return User::register($login, $password);
    }
}