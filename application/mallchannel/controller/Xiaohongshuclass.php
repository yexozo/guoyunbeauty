<?php
namespace app\mallchannel\controller;

use think\Controller;
use GuzzleHttp\Client;
use app\mallchannel\model\mallchannel_token;

class Xiaohongshuclass extends Controller
{

    public function __construct()
    {

        $this->appid = '4e3d199262be4950a350';
        $this->appsecret = 'ef5e2ea1a798a9ef5e59e968b5adc238';
        $this->timestamp = time();
        $this->baseurl = 'https://ark.xiaohongshu.com/ark/open_api/v3/common_controller';

        //数据库获取refresh_token
        $getrefresh = mallchannel_token::get(['id' => '1']);
        $token_data = $getrefresh->toArray();
        $this->accesstoken = $token_data['access_token'];
        $this->refreshtoken = $token_data['refresh_token'];
        $this->accesstoken_endtime = $token_data['access_token_expires_at'];

    }

    public function callBack() {
        file_put_contents('test.txt', 'haha');
        echo 'haha ';
    }

    //获取令牌
    public function getAccessToken() {
        //公共参数
        $version = '2.0';
        $method = 'oauth.getAccessToken';
        //网页授权获取code
        $code = 'code-c254334d7937444db8222e3c575ec453-82e22e9b81df42f2a91f264376c9b37c';

        //按照规则拼接签名
        $sign_str = $method.'?appId='.$this->appid.'&timestamp='.$this->timestamp.'&version='.$version.$this->appsecret;
        //md5加密
        $sign = md5($sign_str);

        $client = new Client();
        $url = 'https://ark.xiaohongshu.com/ark/open_api/v3/common_controller'; // 接口URL

        $data = [
            'sign' => $sign,
            'appId' => $this->appid,
            'timestamp' => $this->timestamp,
            'version' => $version,
            'method' => $method,
            'code' => $code
        ];

        // 设置请求头，包括 Content-Type
        $headers = [
            'Content-Type' => 'application/json',
            // 还可以添加其他的头部信息
        ];

        // 发送 POST 请求，并指定请求头
        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $data, // 将数组数据自动转为 JSON 格式
        ]);

        $content = $response->getBody()->getContents();

        print_r($content);
    }

    //刷新令牌
    public function refresh() {

        //公共参数
        $version = '2.0';
        $method = 'oauth.refreshToken';

        //按照规则拼接签名
        $sign_str = $method.'?appId='.$this->appid.'&timestamp='.$this->timestamp.'&version='.$version.$this->appsecret;
        //md5加密
        $sign = md5($sign_str);

        $client = new Client();
        $url = 'https://ark.xiaohongshu.com/ark/open_api/v3/common_controller'; // 接口URL

        $data = [
            'sign' => $sign,
            'appId' => $this->appid,
            'timestamp' => $this->timestamp,
            'version' => $version,
            'method' => $method,
            'refreshToken' => $this->refreshtoken
        ];

        // 设置请求头，包括 Content-Type
        $headers = [
            'Content-Type' => 'application/json',
            // 还可以添加其他的头部信息
        ];

        // 发送 POST 请求，并指定请求头
        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $data, // 将数组数据自动转为 JSON 格式
        ]);

        $content = $response->getBody()->getContents();

        //获取token过期时间
        $content = json_decode($content,true);
        print_r($content);
        $number = $content['data']['accessTokenExpiresAt'];
        $accessTokenExpiresAt = floor($number / 1000);

        //一个有效期是7天 提前30分钟刷新
        $ptime = strtotime($this->accesstoken_endtime)-1800;
        //echo $ptime;

        //当创建时间超过7天则更新token
        if(time()>$ptime){

            //更新操作
            mallchannel_token::update([
                'access_token'=>$content['data']['accessToken'],
                'access_token_expires_at'=>date('Y-m-d H:i:s',$accessTokenExpiresAt),
                'refresh_token'=>$content['data']['refreshToken'],
                'refresh_token_expires_at'=>date('Y-m-d H:i:s',floor($content['data']['refreshTokenExpiresAt'] / 1000)),
                'update_time'=>date('Y-m-d H:i:s',time())
            ],['id'=>'1']);
            //echo '更新';
        }


    }

    //获取订单
    public function getOrderList($start_time,$end_time,$page_no,$page_size,&$a=array()) {

        //公共参数
        $version = '2.0';
        $method = 'order.getOrderList';

        //按照规则拼接签名
        $sign_str = $method.'?appId='.$this->appid.'&timestamp='.$this->timestamp.'&version='.$version.$this->appsecret;
        //md5加密
        $sign = md5($sign_str);

        $client = new Client();

        $data = [
            'sign' => $sign,
            'appId' => $this->appid,
            'timestamp' => $this->timestamp,
            'version' => $version,
            'method' => $method,
            'accessToken' => $this->accesstoken,
            'startTime' => $start_time, //1701360000
            'endTime' => $end_time, //1701446400
            'timeType' => 1,
            'pageNo' => $page_no,
            'pageSize' => $page_size
        ];

        // 设置请求头，包括 Content-Type
        $headers = [
            'Content-Type' => 'application/json',
            // 还可以添加其他的头部信息
        ];

        // 发送 POST 请求，并指定请求头
        $response = $client->post($this->baseurl, [
            'headers' => $headers,
            'json' => $data, // 将数组数据自动转为 JSON 格式
        ]);

        $content = $response->getBody()->getContents();
        $result = json_decode($content, true);


        $a[]=$result;

        if($page_no != $result['data']['maxPageNo']){
            $this->getOrderList($start_time,$end_time,$page_no+1,$page_size,$a);
        }

        return $a;

    }

    //获取订单详情
    public function getOrderInfo($orderid) {

        //公共参数
        $version = '2.0';
        $method = 'order.getOrderDetail';

        //按照规则拼接签名
        $sign_str = $method.'?appId='.$this->appid.'&timestamp='.$this->timestamp.'&version='.$version.$this->appsecret;
        //md5加密
        $sign = md5($sign_str);

        $client = new Client();

        $data = [
            'sign' => $sign,
            'appId' => $this->appid,
            'timestamp' => $this->timestamp,
            'version' => $version,
            'method' => $method,
            'accessToken' => $this->accesstoken,
            'orderId' => $orderid
        ];

        // 设置请求头，包括 Content-Type
        $headers = [
            'Content-Type' => 'application/json',
            // 还可以添加其他的头部信息
        ];

        // 发送 POST 请求，并指定请求头
        $response = $client->post($this->baseurl, [
            'headers' => $headers,
            'json' => $data, // 将数组数据自动转为 JSON 格式
        ]);

        $content = $response->getBody()->getContents();
        $result = json_decode($content, true);

        return $result;

    }



}