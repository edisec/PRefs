<?php
namespace process;

use app\model\PrefsModel;
use support\PRefs\PRefsGlobal;
use Workerman\Connection\TcpConnection;

class Prefsps
{
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
        
        PRefsGlobal::set("taskconn",$connection);
        $prefsmodel = new PrefsModel;
        $prefsmodel->load($data);
        echo date("[h:m:d]",time())."Loading finished.\n";
        $connection->send("Loading finished.");
        
    }

    public function onClose(TcpConnection $connection)
    {
        echo date("[h:m:d]",time())."onClose\n";
    }
}