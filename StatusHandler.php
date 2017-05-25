<?php
class StatusHandler
{
    public static function error($msg){
        return array('status' => 'ERROR', 'msg' => $msg);
    }

    public static function success($msg = false){
        if(!$msg)
            return array('status' => 'OK');
        else
            return array('status' => 'OK', 'data' => $msg);
    }
}