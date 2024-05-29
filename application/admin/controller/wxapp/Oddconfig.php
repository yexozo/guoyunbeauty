<?php

namespace app\admin\controller\wxapp;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Oddconfig extends Backend
{

    /**
     * Oddconfig模型对象
     * @var \app\admin\model\wxapp\Oddconfig
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\wxapp\Oddconfig;

    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post(); // 获取表单提交的数据

            // 在这里编写你的业务逻辑代码，比如保存数据、发送邮件等
            $data = json_encode($data['row']);
            $data = json_decode($data,true);

            //更新数据
            $this->model->where(['id'=>1])->update(['oddstatuschangeday'=>$data['oddstatuschangeday']]);

            $this->success();
        }

        $this->view->assign('row', $this->model->table('bt_wxapp_config')->find(1));  //模板变量赋值

        return $this->view->fetch();
    }

}
