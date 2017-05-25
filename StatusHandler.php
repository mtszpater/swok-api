<?php
class StatusHandler
{
    public static function error($msg){
        return array('status' => 'ERROR', 'msg' => $msg);
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