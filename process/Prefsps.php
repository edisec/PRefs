<?php
namespace process;

use app\model\PrefsModel;
use support\PRefs\PRefsGlobal;
use Workerman\Connection\TcpConnection;

class Prefsps
{
    public $ivmap;
    public function onConnect(TcpConnection $connection)
    {
        ini_set('memory_limit',-1);
        echo date("[h:m:d]",time())."onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo date("[h:m:d]",time())."onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        list($instrc,$content) = explode(PRefsGlobal::MSG_SEPERATOR,$data,2);
        switch($instrc){
            case "load":
                PRefsGlobal::set("taskconn",$connection);
                $prefsmodel = new PrefsModel;
                $this->ivmap = $prefsmodel->load($content);
                echo date("[h:m:d]",time())."Loading finished.\n";
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
        echo date("[h:m:d]",time())."onClose\n";
    }
}