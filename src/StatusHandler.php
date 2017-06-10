<?php
class StatusHandler
{
    const NOT_ADMIN = "not admin";
    const NOT_LOGGED = "not logged";
    const STH_WRONG = "sth went wrong";
    const USER_DOES_NOT_EXIST = "user does not exist";

    public static function error($msg) {
        return array('status' => 'ERROR', 'info' => $msg);
    }

    public static function success($msg = null) {
        if(is_null($msg)) {
            return array('status' => 'OK');
        }
        else {
            return array('status' => 'OK', 'data' => $msg);
        }
    }

    public static function not_implemented() {
        return array('status' => 'NOT IMPLEMENTED');
    }
}