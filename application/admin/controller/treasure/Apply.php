<?php

namespace app\admin\controller\treasure;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use GuzzleHttp\Client;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Apply extends Backend
{

    /**
     * Apply模型对象
     * @var \app\admin\model\treasure\Apply
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\treasure\Apply;
        $this->view->assign("isrepayStatusList", $this->model->getIsrepayStatusList());
    }



    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $list = $this->model
            ->where($where)
            ->where('name', '=', $this->auth->nickname) // 添加条件：搜索当前用户下的数据
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }

            if($params['event'] == '0' || $params['points'] == '0'){
                $this->error('事件和金额不能留空');
            }

            $params['name'] = $this->auth->nickname;
            $params['createtime'] = date('Y-m-d H:i:s',time());
            $params['progress'] = '待审核';
            $result = $this->model->allowField(true)->save($params);
            Db::commit();

            // 发送 POST 请求 （推送模板消息给指定用户）
            $url = 'https://guoyunstore.com/weixin/activity/muban/guoyun.php'; // 目标 URL
            $data = ['openid' => 'olOzA6jkUkIe_-K2wSAHppVrbfQM', 'name' => $params['name'], 'event' => $params['event'], 'points' => $params['points'], 'createtime' => $params['createtime']]; // 要发送的数据
            $client = new Client(); // 创建 Guzzle 客户端实例
            $response = $client->post($url, ['form_params' => $data]); // 发送 POST 请求
            $statusCode = $response->getStatusCode(); // 获取 HTTP 状态码
            $body = $response->getBody()->getContents(); // 获取响应体内容
            /*
            if ($statusCode == 200) {
                // 处理成功的响应
                echo 'Success: ' . $body;
            }*/



        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }

    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        //获取事件进行步骤
        if($row['progress'] != '待审核'){
            $this->error('该请求已审核或者已打款，无法修改，请联系相关人员');
        }

        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

    public function del($ids = null)
    {
        $row = $this->model->get($ids);
        //获取事件进行步骤
        if($row['progress'] != '待审核'){
            $this->error('该请求已审核或者已打款，无法删除，请联系相关人员');
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
     * 审核
     */
    public function isrepay($ids)
    {
        if ($this->request->isAjax()) {  //ajax请求
            $row = $this->model->get($ids);
            //echo $row['progress'];exit;
            //获取事件进行步骤
            if($row['progress'] != '已打款'){
                $this->error('该条记录尚未打款，无法回款操作，请确认');
            }

            //获取小金库数据 更新在表盘
            $con=mysqli_connect("127.0.0.1","beauty","123789","beauty");
            //mysqli_select_db($db, 'order');  //假设建立连接时未指定数据库则选择使用的数据库，切换使用的数据库
            if (mysqli_connect_errno($con))
            {
                echo "连接 MySQL 失败: " . mysqli_connect_error();
            }
            mysqli_query($con,"SET NAMES UTF8MB4");

            $this->model::update([
                'isrepay_status'=>'待审核'
            ],['id'=>$ids]);

            $this->success('操作成功', null, ['id' => $ids]);




            //获取用户的登录信息（admin表）
            //$userid = $this->auth->id;
            //$username= $this->auth->username;

            //return json(['code' => $row['isrepay_status'], 'msg' => '操作成功']);

        }
    }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
