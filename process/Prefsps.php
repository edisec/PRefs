<?php
namespace process;

use app\model\PrefsModel;
use support\PrefsGlobal;
use support\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Workerman\Connection\TcpConnection;

class Prefsps
{
    public $ivmap;
    public function onConnect(TcpConnection $connection)
    {
        ini_set('memory_limit',-1);
        Log::debug("onConnect");
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        Log::debug("onWebSocketConnect");
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        list($instrc,$content) = explode(PRefsGlobal::MSG_SEPERATOR,$data,2);
        switch($instrc){
            case "load":
                PRefsGlobal::set("taskconn",$connection);
                $prefsmodel = new PrefsModel;
                $this->ivmap = $prefsmodel->load($content);
                Log::debug("Loading finished");
                $connection->send("Loading finished.");
                break;
            case "getCaller":
                $content = json_decode($content,true);
                $prefsmodel = new PrefsModel;
                $connection->send(json_encode($prefsmodel->getCaller($content,$this->ivmap)));
                break;
        }
        
        
    }

    public function onClose(TcpConnection $connection)
    {
        Log::debug("onClose");
    }
}