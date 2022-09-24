<?php
namespace app\model;
use PhpParser\Error;
use PhpParser\ErrorHandler;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use app\model\Prefs\PRefsVisitor;

class PrefsModel{
    public function load($scandir)
    {

        $ErrorHandler = new class implements ErrorHandler
        {
            public function handleError(Error $error)
            {
                echo "Parse error: {$error->getMessage()}" . PHP_EOL;
            }
        };
        //Prepare traverser
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new ParentConnectingVisitor);
        $Engine = new PRefsVisitor();
        $traverser->addVisitor($Engine);

        //Prepare php file iterator

        if (is_dir($scandir)) {
            $Directory = new \RecursiveDirectoryIterator($scandir);
            $Iterator = new \RecursiveIteratorIterator($Directory);
            $codefiles = new \RegexIterator($Iterator, '/^.+\.php$/i', \RecursiveRegexIterator::MATCH);
        } else {
            $codefiles = array($scandir);
        }

        //Start iterate and get contents to traverse
        foreach ($codefiles as $codefile) {
            $code = file_get_contents($codefile);
            $stmts = $parser->parse($code, $ErrorHandler);
            $Engine->filename = $codefile;
            $traverser->traverse($stmts);
        }

        return $Engine->ivmap;
    }

    public function getCaller($data,&$ivmap){
        $caller = array();
        if(empty($methodname = $data['Method'])) return $caller;
        if(empty($methodtype = $data['MethodType'])) return $caller;
        if($methodtype=='ClassMethod' && array_key_exists($methodname,$ivmap->MethodCallNodes)){
            $caller["name"] = $methodname;
            $caller["type"] = $methodtype;
            $caller["content"] = $ivmap->MethodCallNodes[$methodname];
        }

        if($methodtype=='Function_' && array_key_exists($methodname,$ivmap->NonEndFuncCalls)){
            $caller["name"] = $methodname;
            $caller["type"] = $methodtype;
            $caller["content"] = $ivmap->NonEndFuncCalls[$methodname];
        }

        return $caller;
    }
}