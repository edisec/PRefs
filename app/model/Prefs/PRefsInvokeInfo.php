<?php
namespace app\model\Prefs;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use support\PRefs\PRefsGlobal;

class PRefsInvokeInfo
{
    public $Class = "null";
    public $Method = "null";
    public $MethodType;
    public $Call;
    public $Name;
    public $Filename;
    public $key;
    public function __construct($node)
    {
        $printer = PRefsGlobal::getPrinter();
        $this->Call = $printer->prettyPrint([$node]);
        $this->Name = $printer->prettyPrint([$node->name]);

        $tmp = $node->getAttribute('parent');
        while (!empty($tmp) && !($tmp instanceof ClassMethod || $tmp instanceof Function_)) {
            $tmp = $tmp->getAttribute('parent');
        }
        if($tmp){
            $this->Method = strval($tmp->name);
            $mtp = explode("\\",get_class($tmp));
            $this->MethodType = end($mtp);
        }

        while (!empty($tmp) && !($tmp instanceof Class_)) {
            $tmp = $tmp->getAttribute('parent');
        }
        if($tmp) $this->Class = strval($tmp->name);


        $this->Filename = strval($node->getAttribute('filename'));
        $this->startLine = $node->getAttribute('startLine');
        $this->key = "$this->Filename##$this->startLine##".uniqid();
    }

   
}
