<?php
namespace app\wechat\controller;

use app\wechat\model\Test2;
use app\wechat\model\Demo;
use think\Controller;

class Test extends Controller
{
    public function readData()
    {

        echo '<pre>';
        $wechatResponse = Test2::select();

        //json输出数据
        print_r(json_encode($wechatResponse,true));

        //数组方式输出
        $userArray = collection($wechatResponse)->toArray(); //将数据集转换为数组
        print_r($userArray);

    }

    public function readData_more()
    {


        $model = new Demo();

        // 获取第一个表的数据
        $data1 = $model->getDataFromTable1();

        // 获取第二个表的数据
        $data2 = $model->getDataFromTable2();

        // 处理数据并返回...
        print_r(collection($data2)->toArray());

    }
}