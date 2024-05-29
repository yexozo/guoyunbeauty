<?php

namespace app\admin\controller\treasure;

use app\common\controller\Backend;
use think\Request;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Idnex extends Backend
{

    /**
     * Idnex模型对象
     * @var \app\admin\model\treasure\Idnex
     */
    protected $model = null;



    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\treasure\Idnex;
        $this->view->assign("isrepayStatusList", $this->model->getIsrepayStatusList());
    }

    public function ajaxAction(Request $request)
    {
        // 检查请求是否为 AJAX 请求
        if (!$request->isAjax()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        // 处理业务逻辑...
        $data = ['message' => 'Hello, AJAX!'];

        // 返回 JSON 响应
        return json($data);
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
