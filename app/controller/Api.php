<?php

namespace app\controller;

use app\model\PrefsModel;
use support\PRefs\PRefsGlobal;
use support\Request;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Protocols\Http\Response;
use Workerman\Protocols\Http\ServerSentEvents;

class Api
{
    private $ivmap;
    private $task_connection;
    public function load(Request $request)
    {
        $dirname = $request->get('dirname');
        if (!empty($dirname) && file_exists($dirname)) {
            $this->task_connection = new AsyncTcpConnection('text://127.0.0.1:8888');
            $this->task_connection->send($dirname);
            $this->task_connection->onMessage = function(AsyncTcpConnection $task_connection, $task_result){
            
                
                PRefsGlobal::get("sse")->send(new ServerSentEvents(['event' => 'message', 'data' => $task_result]));
                if($task_result == "Loading finished."){
                    $task_connection->close();
                    PRefsGlobal::get("sse")->close();
                }
            };
            $this->task_connection->connect();
            return ok("success");
        }
        return error(404,"Wrong dirname!");
        
    }

    public function getSourceFile(Request $request){
        $filename = $request->get('filename');
        if(file_exists($filename)){
            return ok(file_get_contents($filename));
        }
    }

    public function getCaller(Request $request){
        if(!empty($data = $request->post('node'))){
            $prefsmodel = new PrefsModel;
            return ok($prefsmodel->getCaller($data,$this->ivmap));
        }
        return error(500,'Failed to parse the json.');
    }

    public function establishsse(Request $request){
        $connection = $request->connection;
        if ($request->header('accept') === 'text/event-stream') {
            // 首先发送一个 Content-Type: text/event-stream 头的响应
            $connection->send(new Response(200, ['Content-Type' => 'text/event-stream','Access-Control-Allow-Origin'=>'*']));
            PRefsGlobal::set("sse",$connection);
            return new ServerSentEvents(['event' => 'message', 'data' => 'hello', 'id'=>1]);
        }
        return ok("ok");
    }

    
    
}
