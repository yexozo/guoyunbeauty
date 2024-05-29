<?php

namespace app\wechat\controller;

//use app\wechat\model\Score;
use app\wechat\model\Youzan_customer;
use app\wechat\model\Wx_crm;

/**
 * 微信接口端
 */

class Index
{

    public function readData()
    {

        $getyzid = (new Youzan_customer)->name('youzan_order')
            ->where(['unionid' => 'oDt7_sltHWYNCXfmFR_-fqhedaPA', 'state' => '1'])
            ->select();
        $yz_id = (collection((array)$getyzid)->toArray())[0]['yz_openid'];
        echo $yz_id;

        /*
        $accessToken = '221ea1bec4f924600dcf9fc87cfb038';
        $client = new Client($accessToken);

        $method = 'youzan.item.get';
        $apiVersion = '3.0.0';

        $params = [
            'alias' => 'fa8989ad342k',
        ];

        $response = $client->post($method, $apiVersion, $params);
        var_dump($response);*/


    }

    public function api()
    {
        define("TOKEN", "guoyun123");
        if (!isset($_GET['echostr'])) {
            $this->responseMsg();
        }else{
            $this->valid();
        }

    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function checkSignature()
    {

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }

    }

    public function responseMsg()
    {
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknow msg type: ".$RX_TYPE;
                    break;
            }
            $this->logger("T ".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    private function receiveEvent($object)
    {
        $content = "";
        $openid = $object->FromUserName;
        switch ($object->Event)
        {
            case "subscribe":
                $content = "欢迎探访国韵的一隅之地，\n领取这一道起源于岭南的新中式护肤配方。\n\n缘起岭南，游目自然\n我们看见自然本草的疗愈力量\n我们相信中式疗法的长效平衡\n我们珍视内在持续的身心之美\n\n亲爱的新朋友\n希望你和我们一起\n在护肤中获得疗愈\n在自然中感受新生\n\n国韵，与你分享有关美的一切";
                $content .= (!empty($object->EventKey))?("等你好久啦，既然来了就留下来吧2".str_replace("qrscene_","",$object->EventKey)):"";   //扫码进来的

                //判断是否首次关注，首次关注插入数据库用户信息
                $usernum = Wx_crm::where('openid','=',$openid)->count();
                if($usernum<1){
                    //获取用户unionid
                    $GetUserInfo = new Getuserinfo;
                    $userinfo = $GetUserInfo->get_user_info($openid);
                    $unionid = $userinfo['unionid'];

                    //首次入库给用户加积分
                    //获取有赞id
                    $getyzid = (new Youzan_customer)->name('youzan_order')
                        ->where(['unionid' => $unionid, 'state' => '1'])
                        ->select();
                    // 获取结果数量
                    $count = count($getyzid);

                    if($count>'0'){
                        $yz_id = (collection((array)$getyzid)->toArray())[0]['yz_openid'];
                        //加载有赞加积分函数
                        $youzaninfo = new YouzanClass;
                        $youzaninfo->increasePoints('首关福利','20',$yz_id,'increase');

                        //插入数据
                        Wx_crm::create([
                            'openid'  =>  $openid,
                            'unionid' =>  $unionid,
                            'subcribe' =>  1,
                            'iswxapp' =>  1
                        ]);
                    }else{
                        //插入数据
                        Wx_crm::create([
                            'openid'  =>  $openid,
                            'unionid' =>  $unionid,
                            'subcribe' =>  1
                        ]);
                    }


                }else{
                    //非首次关注，且关注状态为0的用户，做更新操作
                    Wx_crm::update([
                        'deltime'=>0,
                        'subcribe'=>1
                    ],['openid'=>$openid]);

                }



                break;
            case "unsubscribe":
                $content = "取消关注";

                $usernum = Wx_crm::where('openid','=',$openid)->count();
                if($usernum>0){
                    //更新关注状态及记录删除时间
                    Wx_crm::update([
                        'deltime'=>date('Y-m-d H:i:s',time()),
                        'subcribe'=>0
                    ],['openid'=>$openid]);
                }


                break;
            case "SCAN":
                $content = "扫描场景 ".$object->EventKey;
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "COMPANY":
                        $content = "123";
                        break;

                    case "EWM":
                        $fromUsername = $object->FromUserName;
                        $content = "";
                        $resultStr_event_EWM = $this->transmitImage_EWM($object, $content);
                        return $resultStr_event_EWM;
                        break;

                    default:
                        $content = "点击菜单：".$object->EventKey;
                        break;
                }
                break;
            case "LOCATION":
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        switch ($keyword)
        {

            case "文本":
                $content = "这是个文本消息";
                break;
            case "图文":
            case "单图文":
                $content[] = array("Title"=>"单图文标题", "Description"=>"单图文内容", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                break;
            case "多图文":
                $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                break;
            case "音乐":
                $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3");
                break;

            case "cool":
            case "空瓶打卡":
                $content = array("MediaId"=>"lIQGh3eclzHTSc4_AJAy-5m6uP73UkY_bWSEDvt4f3QJEAifO5dHe5pZ7ls8pkvF");
                break;
            default:

                //1
                $content = "嘿 我是小韵";

                /*
                //2 转入客服消息
                $resultStr_TCS = $this->transfer_customer_service($object);
                return $resultStr_TCS;*/
                break;
        }
        if(is_array($content)){
            if (isset($content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }else if (isset($content['MediaId'])){
                $result = $this->transmitImage($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
			<MediaId><![CDATA[%s]]></MediaId>
		</Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $textTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[image]]></MsgType>
		$item_str
		</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 10000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }



}
