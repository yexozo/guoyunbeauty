<?php
namespace app\jushuitan\controller;

use zmoyi\JuShuiTan\Auth\Auth;
use zmoyi\JuShuiTan\Api\ApiRequest;
use zmoyi\JuShuiTan\Api\Common\ServeHttp;
use app\jushuitan\model\Jst_token;

/**
 * 聚水潭口端
 */
class Jstclass
{
    
    public function __construct()
    {
        
        //获取本地数据token
        $gettoken = jst_token::get(['id' => '1']);
        $wx_token_data = $gettoken->toArray();
        $this->access_token = $wx_token_data['access_token'];
        
        //配置信息
        $this->config = [
            'authUrl' => 'https://openweb.jushuitan.com/auth',
            'baseUrl' =>'https://openapi.jushuitan.com/',  //正式环境：https://openapi.jushuitan.com/  测试环境：https://dev-api.jushuitan.com/
            'app_Key' => 'dfda845df3284e71836fa080796475c1',
            'app_Secret'=> '35aec84266f643f9bc4c6a595557d353',
            'access_token' => $wx_token_data['access_token']
        ];

    }
    
    public function test()
    {
        
        $data = [
            'shop_id' => '15216987',
            'modified_begin' => '2023-12-01 00:00:00',
            'modified_end' => '2023-12-02 00:00:00'
            //'sku_ids'=>'D.C.24'
            
        ]; 
        $apiRequest = new ApiRequest($this->config);
        /**
        * ServeHttp::XXX为内部定义的接口路由常量
        * 也可以直接传路由地址，如：/open/orders/out/simple/query
        */
        $response = $apiRequest->request(ServeHttp::QUERY_ORDERS_SINGLE,$data);
        
        echo '<pre>';
        print_r($response);

    }
    
    
    
    public function uptoken()
    {
        
        $config = [
            'authUrl' => 'https://openweb.jushuitan.com/auth',
            'baseUrl' =>'https://openapi.jushuitan.com/',
            'app_Key' => 'dfda845df3284e71836fa080796475c1',
            'app_Secret'=> '35aec84266f643f9bc4c6a595557d353',
            'access_token' => ''
        ];
        
        $Auth = new Auth($config);
        $randcode = randomstr(6);
        $getaccessToken = $Auth->getAccessToken($randcode);
        
        //获取接口过期时间
        $api_etime = date('Y-m-d H:i:s',time()+$getaccessToken['data']['expires_in']);
        
        //一个有效期是30天-864000秒 提前10天刷新
        $ptime = strtotime($api_etime)-864000;
        
        //当创建时间超过20天则更新token
        if(time()>$ptime){
            $refresh_token = $getaccessToken['data']['refresh_token'];  //更新token的凭证
            $refToken = $Auth->refreshToken($refresh_token);
            //print_r($refToken);
            
            //更新操作
            jst_token::update([
                'access_token'=>$refToken['data']['access_token'],
                'refresh_token'=>$refToken['data']['refresh_token'],
                'ctime'=>date('Y-m-d H:i:s',time()),
                'etime'=>date('Y-m-d H:i:s',time()+$refToken['data']['expires_in'])
            ],['id'=>'1']);
            
        }
        
    }
    
    
}