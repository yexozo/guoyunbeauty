<?php

namespace app\admin\controller\treasure;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use GuzzleHttp\Client;

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

        //设置用户权限
        $this->assignconfig('checkout', $this->auth->check('treasure/idnex/checkout'));
        $this->assignconfig('isback', $this->auth->check('treasure/idnex/isback'));
        //$this->assignconfig('ajax', $this->auth->check('treasure/idnex/ajax'));
    }

    /**
     * 详情
     */
    public function detail($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }



    /**
     * 是否回款审核
     */
    public function isback($ids)
    {
        if ($this->request->isAjax()) {  //ajax请求
            $row = $this->model->get($ids);
            //echo $row['status'];exit;

            if($row['isrepay_status'] == '已回款'){
                $this->error('该请求已回款，请勿重复操作');
            }

            //获取事件进行步骤
            if($row['progress'] == '已打款' and $row['isrepay_status'] == '待审核'){
                //更新状态
                $this->model::update([
                    'isrepay_status'=>'已回款'
                ],['id'=>$ids]);

                //获取小金库数据 更新在表盘
                $con=mysqli_connect("127.0.0.1","beauty","123789","beauty");
                //mysqli_select_db($db, 'order');  //假设建立连接时未指定数据库则选择使用的数据库，切换使用的数据库
                if (mysqli_connect_errno($con))
                {
                    echo "连接 MySQL 失败: " . mysqli_connect_error();
                }
                mysqli_query($con,"SET NAMES UTF8MB4");
                //扣除打款金额
                $sql = 'update `bt_treasure_val` set `points` = `points`+'.$row['points'].' where `id` = 1';
                mysqli_query($con,$sql);

                //获取小金库金额
                $sql = 'select * from `bt_treasure_val` where `id` = 1';
                $query_1 = mysqli_query($con,$sql);
                $res_1 = mysqli_fetch_array($query_1);
                $points = $res_1['points'] ?? 0;

                //获取小金库回款金额
                $sql = 'select sum(points) as dhk_points from `bt_treasure_chest` where `progress` = "已打款" and `isrepay_status` != "已回款"';
                $query_2 = mysqli_query($con,$sql);
                $res_2 = mysqli_fetch_array($query_2);
                $isback_count = $res_2['dhk_points'] ?? 0;

                //待审核数
                $sql = 'select count(points) as count from `bt_treasure_chest` where `progress` = "待审核"';
                $query_4 = mysqli_query($con,$sql);
                $res_4 = mysqli_fetch_array($query_4);
                $points_count = $res_4['count'] ?? 0;

                $this->success('操作成功', null, ['id' => $ids,'code' => $row['isrepay_status'],'points' =>$points,'dhk_points' =>$isback_count,'points_count' =>$points_count]);
                //return json(['code' => $row['isrepay_status'],'points' =>$points,'points_count' =>$points_count]);
            }else{
                $this->error('操作失败！该记录尚未打款或未提交回款审核');
            }


            //获取用户的登录信息（admin表）
            //$userid = $this->auth->id;
            //$username= $this->auth->username;
        }
    }



    /**
     * 打款
     */
    public function checkout($ids)
    {
        if ($this->request->isAjax()) {  //ajax请求
            $row = $this->model->get($ids);
            //echo $row['status'];exit;

            //获取事件进行步骤
            if($row['progress'] != '已打款'){

                //更新状态
                $this->model::update([
                    'paytime'=>date('Y-m-d H:i:s',time()),
                    'progress'=>'已打款'
                ],['id'=>$ids]);

                //获取小金库数据 更新在表盘
                $con=mysqli_connect("127.0.0.1","beauty","123789","beauty");
                //mysqli_select_db($db, 'order');  //假设建立连接时未指定数据库则选择使用的数据库，切换使用的数据库
                if (mysqli_connect_errno($con))
                {
                    echo "连接 MySQL 失败: " . mysqli_connect_error();
                }
                mysqli_query($con,"SET NAMES UTF8MB4");
                //扣除打款金额
                $sql = 'update `bt_treasure_val` set `points` = `points`-'.$row['points'].' where `id` = 1';
                mysqli_query($con,$sql);

                //获取小金库金额
                $sql = 'select * from `bt_treasure_val` where `id` = 1';
                $query_1 = mysqli_query($con,$sql);
                $res_1 = mysqli_fetch_array($query_1);
                $points = $res_1['points'] ?? 0;

                //获取小金库回款金额
                $sql = 'select sum(points) as dhk_points from `bt_treasure_chest` where `progress` = "已打款" and `isrepay_status` != "已回款"';
                $query_2 = mysqli_query($con,$sql);
                $res_2 = mysqli_fetch_array($query_2);
                $isback_count = $res_2['dhk_points'] ?? 0;

                //待审核数
                $sql = 'select count(points) as count from `bt_treasure_chest` where `progress` = "待审核"';
                $query_4 = mysqli_query($con,$sql);
                $res_4 = mysqli_fetch_array($query_4);
                $points_count = $res_4['count'] ?? 0;

                $this->success('操作成功', null, ['id' => $ids,'code' => $row['isrepay_status'],'points' =>$points,'dhk_points' =>$isback_count,'points_count' =>$points_count]);
                //return json(['code' => $row['isrepay_status'],'points' =>$points,'points_count' =>$points_count]);
            }else{
                $this->error('操作失败！不能重复打款!');
            }


            //获取用户的登录信息（admin表）
            //$userid = $this->auth->id;
            //$username= $this->auth->username;



        }
    }

    /**
     * 审核
     */
    public function ajax($ids)
    {
        if ($this->request->isAjax()) {  //ajax请求
            $row = $this->model->get($ids);
            //echo $row['progress'];exit;

            //获取事件进行步骤
            if($row['progress'] == '待审核'){

                // 发送 POST 请求 （推送模板消息给指定用户）
                $url = 'https://guoyunstore.com/weixin/activity/muban/guoyun.php'; // 目标 URL
                $data = ['openid' => 'olOzA6u3D9ckzeioiB7wA6AbNKZc', 'name' => $row['name'], 'event' => $row['event'], 'points' => $row['points'], 'createtime' => $row['createtime']]; // 要发送的数据
                $client = new Client(); // 创建 Guzzle 客户端实例
                $response = $client->post($url, ['form_params' => $data]); // 发送 POST 请求
                $statusCode = $response->getStatusCode(); // 获取 HTTP 状态码
                $body = $response->getBody()->getContents(); // 获取响应体内容
                /*
                if ($statusCode == 200) {
                    // 处理成功的响应
                    echo 'Success: ' . $body;
                }*/


                //更新状态
                $this->model::update([
                    'updatetime'=>date('Y-m-d H:i:s',time()),
                    'progress'=>'已审核'
                ],['id'=>$ids]);


                //获取小金库数据 更新在表盘
                $con=mysqli_connect("127.0.0.1","beauty","123789","beauty");
                //mysqli_select_db($db, 'order');  //假设建立连接时未指定数据库则选择使用的数据库，切换使用的数据库
                if (mysqli_connect_errno($con))
                {
                    echo "连接 MySQL 失败: " . mysqli_connect_error();
                }
                mysqli_query($con,"SET NAMES UTF8MB4");

                //获取小金库金额
                $sql = 'select * from `bt_treasure_val` where `id` = 1';
                $query_1 = mysqli_query($con,$sql);
                $res_1 = mysqli_fetch_array($query_1);
                $points = $res_1['points'] ?? 0;

                //待审核数
                $sql = 'select count(points) as count from `bt_treasure_chest` where `progress` = "待审核"';
                $query_4 = mysqli_query($con,$sql);
                $res_4 = mysqli_fetch_array($query_4);
                $points_count = $res_4['count'] ?? 0;

                $this->success('操作成功', null, ['id' => $ids,'code' => $row['isrepay_status'],'points' =>$points,'points_count' =>$points_count]);

            }else{
                $this->error('操作失败！不能重复审核!');
            }


            //获取用户的登录信息（admin表）
            //$userid = $this->auth->id;
            //$username= $this->auth->username;

            //return json(['code' => $row['isrepay_status'], 'msg' => '操作成功']);

        }
    }


    public function del($ids = null)
    {
        $row = $this->model->get($ids);
        //获取事件进行步骤
        if($row['progress'] == '已打款'){
            $this->error('该请求已打款，无法删除，请联系相关人员');
        }

        if (false === $this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ?: $this->request->post("ids");
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = $this->model->where($pk, 'in', $ids)->select();

        $count = 0;
        Db::startTrans();
        try {
            foreach ($list as $item) {
                $count += $item->delete();
            }
            Db::commit();
        } catch (PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were deleted'));
    }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
