<?php

namespace app\wechat\controller;

use app\wechat\model\Wx_token;
use fast\Http;

class GetUserInfo
{
    public function __construct($appid = "wx6d6fc21a86fe49e0", $appsecret = "100b2ba06f7b635b3d82131710d1517d")
    {
        if($appid){
            $this->appid = $appid;
        }
        if($appsecret){
            $this->appsecret = $appsecret;
        }

        $gettoken = wx_token::get(['pname' => 'guoyun']);

        //$this->access_token
        $wx_token_data = $gettoken->toArray();
        $updatetime = $wx_token_data['update_time'];

        //1800更新一次token
        if($updatetime < date('Y-m-d H:i:s',time()-1800)){

            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $res = Http::get($url);
            $result = json_decode($res, true);
            //save to Database or Memcache
            $access_token = $result["access_token"];

            //更新操作
            wx_token::update([
                'accesstoken'=>$access_token,
                'update_time'=>date('Y-m-d H:i:s',time())
            ],['pname'=>'guoyun']);

            $this->access_token = $access_token;

        }else{
            $access_token = $wx_token_data['accesstoken'];
            $this->access_token = $access_token;
        }

    }

    public function ha()
    {
        echo '更新token';
    }

    public function get_user_info($openid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$openid."&lang=zh_CN";
        $res = Http::get($url);
        return json_decode($res, true);
    }

    public function get_user_list()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$this->access_token."&next_openid=";
        $res = Http::get($url);
        $data = json_decode($res, true);
        echo '<pre>';
        //print_r($data['data']['openid']);

        $con=mysqli_connect("127.0.0.1","beauty","123789","beauty");
        //mysqli_select_db($db, 'order');  //假设建立连接时未指定数据库则选择使用的数据库，切换使用的数据库
        if (mysqli_connect_errno($con))
        {
            echo "连接 MySQL 失败: " . mysqli_connect_error();
        }
        mysqli_query($con,"SET NAMES UTF8MB4");
        for($i=0;$i<count($data['data']['openid']);$i++){
            $openid = $data['data']['openid'][$i];

            $userinfo = $this->get_user_info($openid);
            $unionid = $userinfo['unionid'];
            $createtime = date('Y-m-d H:i:s', $userinfo['subscribe_time']);
            $updatetime = date('Y-m-d H:i:s', time());
            $subcribe = $userinfo['subscribe'];

            $sql = 'SELECT * FROM `bt_wx_crm` where `openid` = "'.$openid.'"';
            $query_userlist = mysqli_query($con,$sql);
            $num_userlist = mysqli_num_rows($query_userlist);
            if($num_userlist < '1'){
                $sql = 'INSERT INTO `beauty`.`bt_wx_crm` (`id`, `openid`, `unionid`, `createtime`, `updatetime`, `subcribe`) 
                    VALUES 
                    (NULL,
                     "'.$openid.'",
                      "'.$unionid.'",
                       "'.$createtime.'",
                        "'.$updatetime.'",
                          "'.$subcribe.'")';
                mysqli_query($con,$sql);
            }


        }
    }
    
    //发送客服消息，已实现发送文本，其他类型可扩展
    public function send_custom_message($touser, $type, $data)
    {
        $msg = array('touser' =>$touser);
        switch($type)
        {
            case 'text':
                $msg['msgtype'] = 'text';
                $msg['text']    = array('content'=> urlencode($data));
                break;
			case 'image':
                $msg['msgtype'] = 'image';
                $msg['image']    = array('media_id'=> urlencode($data));
                break;
            case 'news':
				$msg['msgtype'] = 'news';
				$msg['news'] = array("articles"=>json_decode($data));
                break;
            
            case 'miniprogrampage':
                $msg['msgtype'] = 'miniprogrampage';
                $msg['miniprogrampage']    = array( "title"=>urlencode($data),
                                            "appid"=>"wx2cc3ef7aca0b4883",
                                            "pagepath"=>"pages/goods/detail/index?alias=2flqvyze55sx1sv&shopAutoEnter=1",
                                            "thumb_media_id"=>"QUXbsU7w7Vd5O6LVPS07GtIQOBX2sW1Y--DljY474wGsmBVcjl_LQfJyhGKjxB8y"
                                        );
                break;


        }
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->access_token;
        //return $this->https_request($url, urldecode(json_encode($msg)));
        $res = Http::post($url, urldecode(json_encode($msg)));
        return json_decode($res, true);
    }


}