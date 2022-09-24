<?php
namespace app\model\Prefs;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Expr\Eval_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar;
use PhpParser\NodeTraverser;
use support\PRefs\PRefsGlobal;

class PRefsVisitor extends NodeVisitorAbstract
{
    public $ivmap;
    public $filename;
    public function __construct()
    {
        $this->ivmap = new PRefsInvokeMap();
    }

    public function enterNode(Node $node)
    {
        $TargetFunction = PRefsGlobal::TargetFunction;
        $printer = PRefsGlobal::getPrinter();
        if ($node instanceof FuncCall) {
            $ivnode = $this->PrepareNode($node);
            if (in_array($printer->prettyPrint([$node->name]), $TargetFunction)) {
                $this->ivmap->addEndInvokeNode($ivnode);
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }
            $this->ivmap->addNonEndFuncCall($ivnode);
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($node instanceof Eval_ || $node instanceof Include_) {
            //check if concated with Variables
            $name = explode('\\',get_class($node));
            $node->name = new Identifier(end($name));
            if (!$node->expr instanceof Scalar) {
                $ivnode = $this->PrepareNode($node);
                $this->ivmap->addEndInvokeNode($ivnode);
            }

            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }


        if ($node instanceof MethodCall) {
            $ivnode = $this->PrepareNode($node);
            //check recursive call
            if (method_exists($ivnode->Name, "__toString") && (strval($ivnode->Method) == strval($ivnode->Name))) {
                unset($ivnode);
                return;
            }
            $this->ivmap->addMethodCallNode($ivnode);
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
    }


    public function PrepareNode($node)
    {
        $node->setAttribute('filename', $this->filename);
        return new PRefsInvokeInfo($node);
    }
}