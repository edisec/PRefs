<?php
namespace app\model\Prefs;

use support\PRefs\PRefsGlobal;

class UniversalStack{
    private $stack;
    public function __construct()
    {
        $this->stack = [];
    }
    public function push(&$node){
        if(is_array($node)){
            $this->stack[] = array(count($node),&$node);
            return;
        }
        $this->stack[] = &$node;

    }
    public function pop(){
        $crt = &$this->stack[count($this->stack) - 1];
        if(is_array($crt)){
            $crt[PRefsGlobal::ARRAYFRAME_COUNT] -= 1;
            if($crt[PRefsGlobal::ARRAYFRAME_COUNT] == 0){
                array_pop($this->stack);
            }
            return;
        }
        array_pop($this->stack);
    }
    public function &current(){
        $crt = &$this->stack[count($this->stack) - 1];
        if(is_array($crt)){
            return $crt[PRefsGlobal::ARRAYFRAME_BODY][$crt[PRefsGlobal::ARRAYFRAME_COUNT] - 1];
        }
        return $crt;
    }
    public function empty(){
        return empty($this->stack);
    }
}

?>