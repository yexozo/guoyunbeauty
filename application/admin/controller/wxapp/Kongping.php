<?php

namespace app\admin\controller\wxapp;

use app\common\controller\Backend;
use think\Db;
use GuzzleHttp\Client;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Kongping extends Backend
{

    /**
     * Kongping模型对象
     * @var \app\admin\model\wxapp\Kongping
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\wxapp\Kongping;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        //echo $row['status'];exit;

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

        //判断是否已经进行过审核
        if($row['status'] == '审核通过' || $row['status'] == '审核失败'){
            $this->error('这是一条已审核过的数据，不能再次审核');exit;
        }


        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        //强制用户选择审核状态 成功或者失败
        if($params['status'] == '待审核'){
            $this->error('请选择审核状态');exit;
        }

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



            /*发送订阅消息 start*/
            $con=mysqli_connect("127.0.0.1","root","2eec8a8f0f4129a6","weixin");
            //mysqli_select_db($db, 'order');  //假设建立连接时未指定数据库则选择使用的数据库，切换使用的数据库
            if (mysqli_connect_errno($con))
            {
                echo "连接 MySQL 失败: " . mysqli_connect_error();
            }
            mysqli_query($con,"SET NAMES UTF8MB4");
            //echo $openid;
            //$openid = 'oqa4s1CMhG5wJpcwKpsM7Nd0GtEU';
            $openid = $row['openid'];
            $beizhu = $params['msg'];
            if($beizhu == '' || $beizhu == null || $beizhu == null){
                $beizhu = '点击前往领奖';
            }
            if($params['status'] == '审核失败'){
                $page_1 = '';
            }else{
                $page_1 = 'pages/activity/kongping/awards';
            }

            $msg['msgtype'] = 'miniprogrampage';
            $msg = array(
                'touser' =>$openid,
                'template_id'=>'_zlkd5GnOaR5wDhcL3oxbQF7dSpeQHA12it5WX1og7k',
                "page"=>$page_1,
                'data' => array(
                    'phrase2'	=> array(
                        'value' => $params['status']
                    ),
                    'time3' 	=> array(
                        'value' => date('Y年n月j日',time())
                    ),
                    'thing4' 	=> array(
                        'value' => $beizhu
                    )
                )
            );

            //获取小程序accesstoken
            $appid = 'wx49de4f35051349f2';  //wx2cc3ef7aca0b4883 wxadf0f693b17384bd
            $secret = '6359517d4a5feac391f708e9b084f03b';  //99966fac6d9065de646c11f62f884609  35e5ed5c3d20a6c97f11505ff04621fc
            $sql = 'SELECT * FROM wxworkinfo where `title` = "金纳斯国韵小程序"';
            $query_token = mysqli_query($con,$sql);
            $res_token = mysqli_fetch_array($query_token);
            if(time() > $res_token['update_time']+7200){

                $a = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
                $fa=file_get_contents($a);
                $arr1 = explode('access_token":"',$fa);  //openid session_key  unionid
                $arr2 = explode('"',$arr1[1]);
                $res_wxapptoken = $arr2[0];

                $sql = 'UPDATE `weixin`.`wxworkinfo` SET `accesstoken` = "'.$res_wxapptoken.'", `update_time` = "'.time().'" WHERE `wxworkinfo`.`title` ="金纳斯国韵小程序"';
                mysqli_query($con,$sql);

                $accesstoken = $res_wxapptoken;
            }else{
                $accesstoken = $res_token['accesstoken'];
            }

            $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token='.$accesstoken;
            $headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
            //$data=json_encode($data);
            $curl = curl_init();
            //echo $curl;
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);//ssl的版本控制
            if (!empty(urldecode(json_encode($msg)))){
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(json_encode($msg)));
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
            $output = curl_exec($curl);
            curl_close($curl);
            /*发送订阅消息 end*/


            $this->success($beizhu);
            //$this->error('发生了错误');
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }

        $this->success();
    }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
