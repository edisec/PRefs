<?php

namespace app\controller;

use support\PrefsGlobal;
use support\Request;
use support\Log;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Protocols\Http\Response;
use Workerman\Protocols\Http\ServerSentEvents;

class Api
{
    public $getter_connection;
    public function load(Request $request)
    {
        $dirname = $request->get('dirname');
        if (!empty($dirname) && file_exists($dirname)) {
            $task_connection = new AsyncTcpConnection('text://127.0.0.1:8888');
            $task_connection->send(loadmsg($dirname));
            $task_connection->onMessage = function (AsyncTcpConnection $task_connection, $task_result) {
                PRefsGlobal::get("loadsse")->send(ssemessage($task_result));
                if ($task_result == "Loading finished.") {
                    $task_connection->close();
                }
            };
            $task_connection->connect();
            return ok("success");
        }
        return error(404, "Wrong dirname!");
    }

    public function getSourceFile(Request $request)
    {
        $filename = $request->get('filename');
        if (file_exists($filename)) {
            return ok(file_get_contents($filename));
        }
    }

    public function getCaller(Request $request)
    {
        if (!empty($data = $request->post('node'))) {
            if(empty($this->getter_connection)){
                $this->getter_connection = new AsyncTcpConnection('text://127.0.0.1:8888');
                $this->getter_connection->onMessage = function (AsyncTcpConnection $task_connection, $task_result) {
                    PRefsGlobal::get("callersse")->send(ssemessage($task_result));
                };
                $this->getter_connection->connect();
            }
            //controller
            if(empty($data['Method']) || empty($data['MethodType'])){
                PRefsGlobal::get("callersse")->send(ssemessage(array()));
                return ok("success");
            }
            $this->getter_connection->send(getCallermsg(json_encode($data)));
            
            return ok("success");
        }
        return error(500, 'Failed to parse the json.');
    }

    public function loadsse(Request $request)
    {
        return $this->establishsse($request, "loadsse");
    }

    public function callersse(Request $request)
    {
        return $this->establishsse($request, "callersse");
    }
    private function establishsse(Request &$request, $name)
    {
        $connection = $request->connection;
        if ($request->header('accept') === 'text/event-stream') {
            // 首先发送一个 Content-Type: text/event-stream 头的响应
            $connection->send(new Response(200, ['Content-Type' => 'text/event-stream', 'Access-Control-Allow-Origin' => '*']));
            PRefsGlobal::set($name, $connection);
            Log::debug($name." established!");
            return new ServerSentEvents(['event' => 'message', 'data' => 'hello', 'id' => 1]);
        }
        return ok("ok");
    }
}
