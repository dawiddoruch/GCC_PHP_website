<?php

namespace classes;

class Message
{
    /**
     * Display all messages (or just one type)
     * @param string $message_type
     */
    static public function display(string $message_type = '') {
        if(isset($_SESSION['messages']))
            $messages = $_SESSION['messages'];
        else
            return;

        foreach($messages as $type => $list) {
            if($message_type != '' && $message_type != $type) continue;

            foreach($list as $message) {
                echo '<div class="alert alert-'.$type.' alert-dismissible fade show" role="alert">'.$message;
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
            }

            unset($_SESSION['messages'][$type]);
        }
    }

    /*
     * Any messages waiting?
     */
    static public function hasAny(): bool
    {
        if(isset($_SESSION['messages'])) {
            return count($_SESSION['messages']) != 0;
        }
        return false;
    }

    static public function error(string $message) {
        self::message('danger', $message);
    }

    static public function warning(string $message) {
        self::message('warning', $message);
    }

    static public function info(string $message) {
        self::message('info', $message);
    }

    /**
     * Push message into session
     * @param string $type
     * @param string $message
     */
    static public function message(string $type, string $message) {
        if(isset($_SESSION['messages']))
            $messages = $_SESSION['messages'];
        $messages[$type][] = $message;
        $_SESSION['messages'] = $messages;
    }
}