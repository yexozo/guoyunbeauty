<?php

namespace app\admin\controller\wxapp;

use app\common\controller\Backend;

/**
 * 订单列管理
 *
 * @icon fa fa-circle-o
 */
class Odddata extends Backend
{

    /**
     * Odddata模型对象
     * @var \app\admin\model\wxapp\Odddata
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\wxapp\Odddata;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            // 假设时间字段为time_column，请根据实际情况替换成实际的时间字段
            //$timeStart = '2020-01-01 00:00:00'; // 替换为您的开始时间
            //$timeEnd = '2030-12-31 23:59:59';   // 替换为您的结束时间

            $list = $this->model
                ->field('*, COUNT(*) AS quantity, SUM(payment) AS total_amount, SUM( score ) AS total_score')
                ->where($where)
                ->where('pid', '<>', 0) // 添加条件：iid 不等于 0
                //->whereTime('createtime', 'between', [$timeStart, $timeEnd])
                ->order($sort, $order)
                ->group('pd_iid')
                ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['product', 'pd_iid', 'quantity', 'total_amount', 'total_score', 'createtime']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


}
