<?php

namespace classes;

class Session
{
    private int $session_id;
    private int $user_id;
    private string $id;
    private string $timestamp;


    /**
     * Rename session cookie and start session
     */
    public static function start()
    {
        Debug::info(__METHOD__);
        session_name("EAT_ME");
        session_start();
        self::purge();
    }


    /**
     * Should be called when user leaves?
     */
    public static function destroy(int $user_id = -1) {
        Debug::info(__METHOD__);

        if($user_id == -1)
            $user_id = self::getSessionUserID();

        $id = session_id();

        if($user_id != -1)
            DB::query("DELETE FROM `session` WHERE `user_id`={$user_id} AND `id`='{$id}'");

        session_destroy();
    }


    /**
     * Returns user_id if logged in or -1 if not
     * @return int
     */
    public static function getSessionUserID(): int
    {
        Debug::info(__METHOD__);
        if(!isset($_SESSION['user_id']))
            return -1;

        $user_id = (int)$_SESSION['user_id'];

        DB::connect();
        $query = "SELECT * FROM session WHERE id=:id AND user_id=:user_id AND timestamp >= NOW() - INTERVAL 1 HOUR LIMIT 1";

        $stmt = DB::$connection->prepare($query);
        $stmt->execute(['id' => session_id(), 'user_id' => $user_id]);
        $data = $stmt->fetch();

        if(!$data)
            return -1;

        self::regenerateID($data['session_id']);

        return $user_id;
    }


    /**
     * Saves current session to database
     * @param int $user_id
     */
    public static function setSessionUserID(int $user_id) {
        Debug::info(__METHOD__);
        $_SESSION['user_id'] = $user_id;
        DB::connect();
        $query = "INSERT INTO session (user_id, id) VALUES (:user_id, :id)";
        $stmt = DB::$connection->prepare($query);
        $stmt->execute(['user_id' => $user_id, 'id' => session_id()]);
    }


    /**
     * Delete all sessions older than 1 hour
     */
    private static function purge() {
        Debug::info(__METHOD__);
        DB::query("DELETE FROM session WHERE timestamp < NOW() - INTERVAL 1 HOUR");
    }


    /**
     * Generate new session ID if current one is getting old
     * @param $session_id
     */
    private static function regenerateID($session_id) {
        Debug::info(__METHOD__);
        $result = DB::query("SELECT  (timestamp < NOW() - INTERVAL 15 MINUTE) AS old FROM session WHERE session_id={$session_id}");
        $data = $result->fetch();
        if(!$data) return;

        // if session ID is old
        if($data['old'] == 1) {
            session_regenerate_id();
            $id = session_id();
            DB::query("UPDATE `session` SET `id`='{$id}' WHERE `session_id`={$session_id}");
        }
    }
}