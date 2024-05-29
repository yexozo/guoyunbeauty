<?php

namespace app\index\controller;

use app\common\controller\Frontend;

class Test extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
        return $this->view->fetch();
    }

    public function test()
    {
        $this->assign('name', '嘻嘻');
        return $this->view->fetch();
    }

}
