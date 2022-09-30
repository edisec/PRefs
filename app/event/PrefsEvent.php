<?php
namespace app\event;
use support\PrefsGlobal;
use Workerman\Protocols\Http\ServerSentEvents;

class PrefsEvent{
    public function push($data){
        PRefsGlobal::get("taskconn")->send($data);
    }
}
?>