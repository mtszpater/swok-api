<?php
class StatusHandler
{
    public static function error(){
        return array('status' => 'ERROR');
    }

    public static function success($msg = null){
        if(is_null($msg))
            return array('status' => 'OK');
        else
            return array('status' => 'OK', 'data' => $msg);
    }

    public static function not_implemented(){
        return array('status' => 'NOT IMPLEMENTED');
    }
}