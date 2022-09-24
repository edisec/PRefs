<?php

namespace app\controller;

use app\model\PrefsModel;
use support\Request;

class Api
{
    private $ivmap;
    
    public function load(Request $request)
    {
        $dirname = $request->get('dirname');
        if (!empty($dirname) && file_exists($dirname)) {
            $prefsmodel = new PrefsModel;
            $this->ivmap = $prefsmodel->load($dirname);
            return ok($this->ivmap->EndInvokeNodes);
        }
        return error(404,"Wrong dirname!");
        
    }

    public function getSourceFile(Request $request){
        $filename = $request->get('filename');
        if(file_exists($filename)){
            return ok(file_get_contents($filename));
        }
    }

    public function getCaller(Request $request){
        if(!empty($data = $request->post('node'))){
            $prefsmodel = new PrefsModel;
            return ok($prefsmodel->getCaller($data,$this->ivmap));
        }
        return error(500,'Failed to parse the json.');
    }
    
}
