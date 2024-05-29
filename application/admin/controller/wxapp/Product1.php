<?php

namespace app\admin\controller\wxapp;

use app\common\controller\Backend;
use app\jushuitan\controller\Jstclass;
use app\admin\model\wxapp\Product as Product_md;


/**
 * 产品管理
 *
 * @icon fa fa-circle-o
 */
class Product extends Backend
{

    /**
     * Product模型对象
     * @var \app\admin\model\wxapp\Product
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\wxapp\Product;

    }
    
     public function add()  
    {  
        // 处理表单提交  
        if ($this->request->isPost()) {  
            $data = $this->request->post(); // 获取表单提交的数据  
              
            // 在这里编写你的业务逻辑代码，比如保存数据、发送邮件等  
            $data = json_encode($data['row']);
            $data = json_decode($data,true);
            //return $data;
            
            //$getyzid = new Jstclass;
            //$hhh = $getyzid->test();
              
            // 假设保存数据到数据库  
            //插入数据
                    Product_md::create([
                        'product'  =>  $data['product'],
                        'pimage'  =>  $data['pimage'],
                        'content_image'  =>  $data['content_image'],
                        'payment'  =>  $data['payment'],
                        'score'  =>  $data['score'],
                        'skuid'  =>  $data['skuid'],
                        'iid'  =>  $data['iid'],
                        'qty'  =>  $data['qty'],
                        'weigh'  =>  $data['weigh'],
                        'limitation_quantum'  =>  $data['limitation_quantum'],
                        'onlinetime'  =>  $data['onlinetime'],
                        'offlinetime'  =>  $data['offlinetime'],
                        'siteswitch'  =>  $data['siteswitch'],
                    ]);
                    
                    
            /*
            $con=mysqli_connect("127.0.0.1","root","2eec8a8f0f4129a6","weixin");
            //mysqli_select_db($db, 'order');  //假设建立连接时未指定数据库则选择使用的数据库，切换使用的数据库
            if (mysqli_connect_errno($con))
            {
                echo "连接 MySQL 失败: " . mysqli_connect_error();
            }
            mysqli_query($con,"SET NAMES UTF8MB4");
            
            $sql = "INSERT INTO `beauty`.`bt_wxapp_product` (
                `id` ,
                `product` ,
                `pimage` ,
                `content_image` ,
                `payment` ,
                `score` ,
                `skuid` ,
                `iid` ,
                `qty` ,
                `weigh` ,
                `limitation_quantum` ,
                `onlinetime` ,
                `offlinetime` ,
                `siteswitch`
                )
                VALUES (
                NULL , '".$data['product']."', '".$data['pimage']."', '".$data['content_image']."', '".$data['payment']."', '".$data['score']."', '".$data['skuid']."', '".$data['iid']."', '".$data['qty']."', '".$data['weigh']."', '".$data['limitation_quantum']."', '".$data['onlinetime']."', '".$data['offlinetime']."', '".$data['siteswitch']."'
                );";
            mysqli_query($con,$sql);*/
                            
              
            // 返回响应或重定向到其他页面  
            return $this->success('提交成功！');  
        }  
          
        // 渲染模板或显示其他视图  
        return $this->fetch();  
    }  
    
    
    public function test()
    {
        
        echo 'sdf';

    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
