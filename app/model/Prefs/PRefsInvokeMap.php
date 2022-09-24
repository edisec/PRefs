<?php

namespace app\model\Prefs;

class PRefsInvokeMap
{
    public $EndInvokeNodes = [];
    public $NonEndFuncCalls = [];
    public $MethodCallNodes = [];
    public $constructed = false;

    public function addEndInvokeNode(PRefsInvokeInfo $ivnode)
    {
        $this->addNOde($ivnode, $this->EndInvokeNodes);
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
