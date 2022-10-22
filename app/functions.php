<?php

use support\PrefsGlobal;
use Workerman\Protocols\Http\ServerSentEvents;

/**
 * Here is your custom functions.
 */

function error(int $code,$msg)
{
    return json(["code" => $code, "msg" => $msg]);
}

function ok($msg){
    return json(["code" => 200, "msg" => $msg]);
}

function ssemessage($data){
    if(!is_string($data)) $data = json_encode($data);
    return new ServerSentEvents(['event' => 'message', 'data' => $data]);
}

function loadmsg($data){
    return packmessage("load",$data);
}

function getCallermsg($data){
    return packmessage("getCaller",$data);
}

function packmessage($name,$data){
    return $name.PRefsGlobal::MSG_SEPERATOR.$data;
}