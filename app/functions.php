<?php

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
