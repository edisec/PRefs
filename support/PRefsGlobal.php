<?php

namespace support\PRefs;

use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\Encapsed;

class PRefsGlobal
{
    public const ARRAYFRAME_COUNT = 0;
    public const ARRAYFRAME_BODY = 1;
    public static $printer;
    public const TargetFunction = [
        "assert",
        "create_function",
        "array_map",
        "call_user_func",
        "call_user_func_array",
        "usort",
        "uasort",
        "system",
        "exec",
        "shell_exec",
        "passthru",
        "pcntl_exec",
        "popen",
        "proc_open",
        "file_get_contents",
        "file_put_contents",
        "fopen",
        "fread",
        "fgets",
        "fgetss",
        "readfile",
        "file",
        "parse_ini_file",
        "show_source",
        "highlight_file",
        "move_uploaded_file",
        "unlink",
        "session_destroy",
        "extract",
        "parse_str",
        "import_request_variables",
        "ereg",
        "curl_setopt",
        "preg_replace",
        "parse_url",
        "escapeshellcmd",
        "escapeshellarg",
        "class_exists",
        "filter_var",
        "mail"
    ];

    public static function getPrinter()
    {
        if (empty(self::$printer)) {
            self::$printer = new \PhpParser\PrettyPrinter\Standard;
        }
        return self::$printer;
    }

    
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

    public static function recursiveCheckConcat($innerExpr){
        if(empty($innerExpr)) return false;
        if($innerExpr instanceof Variable) return true;
        return self::recursiveCheckConcat($innerExpr->left) || self::recursiveCheckConcat($innerExpr->right);
        
    }

}
