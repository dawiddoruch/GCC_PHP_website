<?php

namespace classes;

class User
{
    private array $attributes = [
        'user_id',
        'login',
        'password',
        'hash',
        'last_login'
    ];


    public function __construct($user_id = 0)
    {
        if($user_id > 0) {
            $user = DB::getOneByID('user', $user_id);
            if($user) {
                $this->userExists = true;
                foreach($this->attributes as $key => $value) {
                    $this->attributes[$key] = $user[$key];
                }
            }
        }
    }


    public static function isLogged(): bool
    {
        if(Session::getSessionUserID() != -1)
            return true;
        return false;
    }


    /**
     * Save user into DB
     * @param string $login
     * @param string $password
     * @return bool
     */
    public static function register(string $login, string $password): bool
    {
        $data = [
            'login' => $login,
            'password' => self::hash($password)
        ];

        if(strlen($login) < 5) {
            Message::info("Login must be at least 5 characters long");
            return false;
        }

        if(strlen($password) < 8) {
            Message::info("Password must be at least 8 characters long");
            return false;
        }

        // check if user with that login already exists and create one if it doesn't
        if(self::user_exists($login)) {
            Message::info("User with that login already exists.");
            return false;
        }

        if(DB::insert('user', $data) == null) {
            Message::info("Can't save user info.");
            return false;
        }

        return true;
    }


    /**
     * Try logging in
     * @param string $login
     * @param string $password
     * @return bool
     */
    public static function login(string $login, string $password): bool
    {
        $user = DB::getOne('user', [['login', $login]]);

        if(!$user) {
            Message::info("Incorrect login or password.");
            return false;
        }

        if(!self::check_password($password, $user['password'])) {
            Message::info("Incorrect login or password.");
            return false;
        }

        Session::setSessionUserID($user['user_id']);
        return true;
    }


    public static function logout() {
        Session::destroy();
    }


    private static function check_password($typed_password, $stored_password): bool
    {
        return password_verify($typed_password, $stored_password);
    }


    private static function user_exists($login): bool
    {
        $user = DB::getOne('user', [['login', $login]]);
        if(!$user) return false;
        return true;
    }


    public static function hash($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}