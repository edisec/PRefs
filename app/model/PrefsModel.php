<?php

namespace app\model;

use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use app\model\Prefs\PRefsVisitor;
use support\PrefsGlobal;


class PrefsModel
{

    public function load($scandir)
    {

        $ErrorHandler = new \PhpParser\ErrorHandler\Collecting;
        //Prepare traverser
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $nameResolver = new \PhpParser\NodeVisitor\NameResolver(null,[
            'preserveOriginalNames' => false,
            'replaceNodes' => false,
        ]);
        $traverser = new NodeTraverser();
        $Engine = new PRefsVisitor();

        $traverser->addVisitor($nameResolver);
        $traverser->addVisitor(new ParentConnectingVisitor);
        $traverser->addVisitor($Engine);

        //Prepare php file iterator

        if (is_dir($scandir)) {
            $Directory = new \RecursiveDirectoryIterator($scandir);
            $Iterator = new \RecursiveIteratorIterator($Directory);
            $codefiles = new \RegexIterator($Iterator, PRefsGlobal::TargetExtRegex, \RecursiveRegexIterator::MATCH);
        } else {
            $codefiles = array($scandir);
        }

        //Start iterate and get contents to traverse
        foreach ($codefiles as $codefile) {
            $code = file_get_contents($codefile);
            $stmts = $parser->parse($code, $ErrorHandler);
            if($stmts !== NULL){
                $Engine->filename = $codefile;
                $traverser->traverse($stmts);
            }else if ($ErrorHandler->hasErrors()) {
                    foreach ($ErrorHandler->getErrors() as $error) {
                        echo "parse error: ".$error->getRawMessage().PHP_EOL;
                    }
            }
        }

        return $Engine->ivmap;
    }


    public function getCaller($data, &$ivmap)
    {
        $methodtype = $data['MethodType'];
        switch ($methodtype){
            case 'ClassMethod':
                return $this->assemble($data,$ivmap->MethodCallNodes);;
            case 'Function_':
                return $this->assemble($data,$ivmap->NonEndFuncCalls);;
        }
    }

    private function assemble($data,$callnodes){
        $caller = array();
        $classname = $data['Class'];
        $methodname = $data['Method'];
        if (array_key_exists($methodname, $callnodes)) {
            $candidate = array_filter($callnodes[$methodname],function($item)use($classname,$methodname){
                if($item->Class == $classname) return $item->Method != $methodname;
                return true;
            });

            if(empty($candidate)) return array();
            
            $caller["name"] = "$classname/$methodname";
            $caller["type"] = $data['MethodType'];
            $caller["content"] = $candidate;
            
            
        }
        return $caller;
    }
}
