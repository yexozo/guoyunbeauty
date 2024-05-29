<?php

namespace app\wechat\controller;


class Class_youzan_adv
{
    public function increasePoints2()
    {
        require_once '/www/wwwroot/guoyunstore.com/weixin/youzanapi/public_class.php';
        $api_yanzhi = new class_youzan_adv();
        $increase_yanzhi= $api_yanzhi->increasePoints('颜值转移',1,'P6GGYCjA697035304380465152','increase');
    }

}
