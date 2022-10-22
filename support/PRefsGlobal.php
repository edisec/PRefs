<?php

namespace support;



class PRefsGlobal
{
    protected static $glbs;
    public const MSG_SEPERATOR = "|@|";
    public const ARRAYFRAME_COUNT = 0;
    public const ARRAYFRAME_BODY = 1;
    protected static $printer;
    public const TargetFunction = [
        "assert",
        "create_function",
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
        "filter_var",
        "mail"
    ];
    public const TargetExtRegex = "/^.+\.php$|^.+\.inc$/i";

    public static function get($key){
        return self::$glbs[$key];
    }

    public static function set($key,&$data){
        self::$glbs[$key] = &$data;
    }
    public static function getPrinter()
    {
        if (empty(self::$printer)) {
            self::$printer = new \PhpParser\PrettyPrinter\Standard;
        }
        return self::$printer;
    }

    
    

}
