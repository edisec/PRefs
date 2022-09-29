<?php

namespace app\model\Prefs;

use Webman\Event\Event;

class PRefsInvokeMap
{
    public $EndInvokeNodes = [];
    public $NonEndFuncCalls = [];
    public $MethodCallNodes = [];
    public $constructed = false;

    public function addEndInvokeNode(PRefsInvokeInfo $ivnode)
    {
        $this->addNode($ivnode, $this->EndInvokeNodes);
        Event::emit("newCall",json_encode(array('name'=>$ivnode->Name,'content'=>$ivnode),JSON_UNESCAPED_UNICODE));
    }

    public function addNonEndFuncCall(PRefsInvokeInfo $ivnode)
    {
        $this->addNode($ivnode, $this->NonEndFuncCalls);
    }

    public function addMethodCallNode(PRefsInvokeInfo $ivnode)
    {
        $this->addNode($ivnode, $this->MethodCallNodes);
    }

    public function addNode(PRefsInvokeInfo $ivnode, array &$procedureNodes)
    {
        #提取method/function的名字作为key
        $key = $ivnode->Name;
        $procedureNodes[$key][] = $ivnode;
        
        
    }

    public function &get_caller(PRefsInvokeInfo $ivnode, array &$nodes)
    {
        $methodname = $ivnode->Method;
        if (array_key_exists($methodname, $nodes)) {
            return $nodes[$methodname];
        }
    }
}
