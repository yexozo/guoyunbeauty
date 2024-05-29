<?php

namespace app\admin\controller\orderrewards;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Datasum extends Backend
{

    /**
     * Datasum模型对象
     * @var \app\admin\model\orderrewards\Datasum
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\orderrewards\Datasum;

    }

    public function index()
    {
        // 设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);

        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }

        // 如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $list = $this->model
            ->field('*, SUM(v) AS zval, COUNT(*) AS count')
            ->where($where)
            ->where('TIME', '>=', '2000-01-01 00:00:00')
            ->where('TIME', '<=', '3000-01-01 00:00:00')
            ->where('phone', '<>', '')
            ->order($sort, $order)
            ->group('phone')
            ->paginate($limit);
        /*
        //主查询+子查询
        $list = $this->model
            ->alias('main')
            ->field('main.*, SUM(main.v) AS zval, COUNT(*) AS count')
            ->where('TIME', '>=', '2024-04-15 23:35:30')
            ->where('TIME', '<=', '2024-04-18 00:00:00')
            ->where('phone', '<>', '')
            ->group('phone')
            ->select();
        */

        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
