<?php

use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\Encapsed;

class PRefsRules{
    public static function ifContainsVar($expr){
        if($expr instanceof Variable) return true;
        if($expr->expr instanceof Encapsed){
            return in_array(true,array_map(function($part){return $part instanceof Variable;},$expr->expr->parts));
        }
        if($expr->expr instanceof Concat){
            $ret = self::recursiveCheckConcat($expr->expr);
            var_dump($ret);
            return $ret;
        }
    }
    
    public static function recursiveCheckConcat($expr){
        if(empty($expr)) return false;
        if($expr instanceof Variable) return true;
        return self::recursiveCheckConcat($expr->left) || self::recursiveCheckConcat($expr->right);
    }
}
?>