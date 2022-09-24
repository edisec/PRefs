<?php
namespace app\controller;

use support\Request;

class Index{
    public function index(Request $request){
        return redirect('index.html');
    }
}