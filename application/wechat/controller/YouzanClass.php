<?php

namespace app\wechat\controller;

use Youzan\Open\Client;
use Youzan\Open\Token;

class YouzanClass
{

    var $clientId = "874d3885cfce1e2aa8";
    var $clientSecret = "398c76cf3b00c1a82a01c93407a8decc";

    //构造函数，获取Access Token
    public function __construct($clientId = "", $clientSecret = "")
    {

        if($clientId){
            $this->clientId = $clientId;
        }
        if($clientSecret){
            $this->clientSecret = $clientSecret;
        }

        $con=mysqli_connect("127.0.0.1","root","2eec8a8f0f4129a6","weixin");
        //mysqli_select_db($db, 'order');  //假设建立连接时未指定数据库则选择使用的数据库，切换使用的数据库
        if (mysqli_connect_errno($con))
        {
            echo "连接 MySQL 失败: " . mysqli_connect_error();
        }
        mysqli_query($con,"SET NAMES UTF8MB4");

        $sql_1 = "SELECT * FROM `youzaninfo` WHERE id = 1";
        $query_1 = mysqli_query($con,$sql_1);
        $rs_1 = mysqli_fetch_array($query_1);
        //hardcode
        $this->lasttime = $rs_1["update_time"];
        $this->access_token = iconv("GBK", "UTF-8", $rs_1["accesstoken"]);


        if (time() > ($this->lasttime + 432000)){  //五天更新一次
            $config['refresh'] = true;  //是否获取refresh_token(可通过refresh_token刷新token)
            $resp = (new Token($this->clientId, $this->clientSecret))->getSelfAppToken('40641384', $config);
            //echo $resp['access_token'];
            //save to Database or Memcache
            $this->access_token = $resp['access_token'];
            $this->lasttime = time();
            //更新数据库中access_token和生成时间戳
            $sql_2 = 'UPDATE `youzaninfo` SET `accesstoken` = "'.iconv("UTF-8", "GBK", $this->access_token).'", `update_time` = "'.time().'" WHERE id = 1';
            mysqli_query($con,$sql_2);
            mysqli_close($con);//关闭数据库，节省资源
        }


    }

    //通过unionid获取yz_openid
    public function getYz_openid($unionid){
        $client = new Client($this->access_token);
        $method = 'youzan.users.info.query';
        $apiVersion = '1.0.0';

        $params = [
            'weixin_union_id' => $unionid,
            'result_type_list' => [
                '1','2'
            ],
        ];

        $response = $client->post($method, $apiVersion, $params);
        //echo "<pre>";
        //print_r($response);
        //echo $response['data']['user_list'][0]['primitive_info']['yz_open_id'];
        return $response;

    }

    //给用户加/减积分
    public function increasePoints($reason,$points,$yz_open_id,$or){
        if($or == 'increase'){
            $or_str = 'youzan.crm.customer.points.increase';
        }else if($or == 'decrease'){
            $or_str = 'youzan.crm.customer.points.decrease';
        }
        $client = new Client($this->access_token);
        $method = $or_str;
        $apiVersion = '4.0.0';

        $params = [
            "params"=>['reason' => $reason,  //积分变动原因
                'points'=> $points,
                'user'=>['account_type'=>5,'account_id'=>$yz_open_id]  //
            ]
        ];

        $response = $client->post($method, $apiVersion, $params);
        //echo '<pre>';
        //var_dump($response);
        //print_r($response);
        return $response;
    }




}