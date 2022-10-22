<?php
namespace app\model\Prefs;

use JsonSerializable;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use support\PrefsGlobal;

class PRefsInvokeInfo implements JsonSerializable
{
    public $NameSpace = "null";
    public $Class = "null";
    public $Method = "null";
    public $MethodType;
    public $Call;
    public $Name;
    public $FileName;
    public $key;
    public function __construct(Node $node)
    {
        $printer = PRefsGlobal::getPrinter();
        
        $this->Call = $node;
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
        if($tmp){
            $this->Class = strval($tmp->name);
            if($nsp = $tmp->namespacedName){
                $this->NameSpace = strval($nsp);
            }
        
        }

        $this->FileName = strval($node->getAttribute('filename'));
        $this->startLine = $node->getAttribute('startLine');
        $this->key = "$this->FileName##$this->startLine##".uniqid();
        
        
    }

    public function jsonSerialize()
    {
        $ret = get_object_vars($this);
        $ret['Call'] = PRefsGlobal::getPrinter()->prettyPrint([$ret['Call']]);
        return $ret;
    }

   
}

