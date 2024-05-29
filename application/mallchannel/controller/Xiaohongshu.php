<?php
namespace app\mallchannel\controller;

use think\Controller;
use GuzzleHttp\Client;
use app\mallchannel\model\xhs_tid;
use think\Request;

class Xiaohongshu extends Controller
{

    public function callback() {
        // 获取当前请求的 Request 对象
        $request = Request::instance();
        $header_timestamp = $request->header('timestamp');
        $header_appkey = $request->header('app-key');
        $header_sign = $request->header('sign');

        $body = file_get_contents('php://input');  //获取body体

        $qurl = '/open_api/msg?';
        $canshu = 'canshu=test';
        $appid = '4e3d199262be4950a350';
        $timestamp = time();
        $appsecret = 'ef5e2ea1a798a9ef5e59e968b5adc238';


        $yq = $qurl.'app-key='.$appid.'&'.$canshu.'&timestamp='.$header_timestamp.$appsecret;
        $sign = md5($yq);

        //$test_sgin = $sign.'--'.$header_sign;
        //file_put_contents(__DIR__.'/test.txt', $test_sgin);
        /*
        if($sign == $header_sign){
            echo '验签成功';
        }*/

        if($header_sign !='' && $header_appkey == $appid){
            $body_arr = json_decode($body,true);
            $database = json_decode($body_arr[0]['data'],true);

            if($body_arr[0]['msgTag'] == 'msg_fulfillment_status_change'){ //订单状态变更
                //查看订单是否存在
                $tidnum = xhs_tid::where('tid','=',$database['orderId'])->count();
                if($tidnum >0){
                    //有该数据则更新
                    $this->pullTidInfo($database['orderId'],'update');
                }else{
                    //没有该数据则添加
                    $this->pullTidInfo($database['orderId'],'inset');
                }


            }




            $json = '{
                success: true,
               error_code: 0,
               error_msg: 0
            }';
            echo $json;
            file_put_contents(__DIR__.'/test.txt', $body_arr[0]['data']);


        }else{
            $json = '{
                success: false,
                error_code: 1001,
                error_msg: missing parameter 
            }';
            echo $json;
            //file_put_contents(__DIR__.'/test.txt', $json);
        }
    }

    //获取订单列表
    public function pullTid() {
        /*
         * //查询操作
        $getrefresh = xhs_tid::get(['id' => '1']);
        $token_data = $getrefresh->toArray();
        echo $token_data['tid'];exit;*/


        $Xiaohongshu = new Xiaohongshuclass;
        $start_time = strtotime('2024-04-01 00:00:00');
        $end_time = strtotime('2024-04-02 00:00:00');
        $tidinfo = $Xiaohongshu->getOrderList($start_time,$end_time,1,50);
        //echo '<pre>';
        //print_r($tidinfo);exit;
        for($i=0;$i<count($tidinfo);$i++){
            //echo $tidinfo[$i]['data']['orderList'][0]['orderId'];
            for($j=0;$j<count($tidinfo[$i]['data']['orderList']);$j++){
                $tid = $tidinfo[$i]['data']['orderList'][$j]['orderId'];  //订单号
                $order_type = $tidinfo[$i]['data']['orderList'][$j]['orderType'];  //订单类型
                switch ($order_type) {
                    case '1':
                        // 执行条件1的操作
                        $order_type = '普通';
                        break;
                    case '2':
                        $order_type = '定金预售';
                        break;
                    case '4':
                        $order_type = '全款预售';
                        break;
                    case '5':
                        $order_type = '换货补发';
                        break;
                    default:
                        $order_type = $order_type;
                }

                $order_status = $tidinfo[$i]['data']['orderList'][$j]['orderStatus'];  //订单状态
                switch ($order_status) {
                    case '1':
                        // 执行条件1的操作
                        $order_status = '已下单待付款';
                        break;
                    case '2':
                        $order_status = '已支付处理中';
                        break;
                    case '3':
                        $order_status = '清关中';
                        break;
                    case '4':
                        $order_status = '待发货';
                        break;
                    case '5':
                        $order_status = '部分发货';
                        break;
                    case '6':
                        $order_status = '待收货';
                        break;
                    case '7':
                        $order_status = '已完成';
                        break;
                    case '8':
                        $order_status = '已关闭';
                        break;
                    case '9':
                        $order_status = '已取消';
                        break;
                    case '10':
                        $order_status = '换货申请中';
                        break;
                    default:
                        $order_status = $order_status;
                }

                $order_aftersales_status = $tidinfo[$i]['data']['orderList'][$j]['orderAfterSalesStatus'];  //售后状态
                switch ($order_aftersales_status) {
                    case '1':
                        // 执行条件1的操作
                        $order_aftersales_status = '无售后';
                        break;
                    case '2':
                        $order_aftersales_status = '售后处理中';
                        break;
                    case '3':
                        $order_aftersales_status = '售后完成(含取消)';
                        break;
                    case '4':
                        $order_aftersales_status = '售后拒绝';
                        break;
                    case '5':
                        $order_aftersales_status = '售后关闭';
                        break;
                    case '6':
                        $order_aftersales_status = '平台介入中';
                        break;
                    default:
                        $order_aftersales_status = $order_aftersales_status;
                }

                $cancel_status = $tidinfo[$i]['data']['orderList'][$j]['cancelStatus'];  //申请取消状态
                if($cancel_status == 0){
                    $cancel_status = '未申请取消';
                }else{
                    $cancel_status = '取消处理中';
                }

                $created_time = date('Y-m-d H:i:s',substr($tidinfo[$i]['data']['orderList'][$j]['createdTime'], 0, 10));  //创建时间
                $paid_time = date('Y-m-d H:i:s',substr($tidinfo[$i]['data']['orderList'][$j]['paidTime'], 0, 10));  //支付时间

                //查看订单是否存在
                $tid_num = xhs_tid::where('tid','=',$tid)->count();
                if($tid_num < 1){
                    //插入数据
                    xhs_tid::create([
                        'tid'  =>  $tid,
                        'order_type'  =>  $order_type,
                        'order_status'  =>  $order_status,
                        'order_aftersales_status'  =>  $order_aftersales_status,
                        'cancel_status'  =>  $cancel_status,
                        'created_time'  =>  $created_time,
                        'paid_time'  =>  $paid_time
                    ]);
                }

            }
        }
    }

    //获取订单详情
    public function pullTidInfo($tid,$or) {

        $Xiaohongshu = new Xiaohongshuclass;
        $tidinfo = $Xiaohongshu->getOrderInfo($tid);
        $tid = $tidinfo['data']['orderId'];  //订单号
        //$order_type = $tidinfo['data']['orderType'];  //订单类型
        switch ($tidinfo['data']['orderType']) {
            case '1':
                // 执行条件1的操作
                $order_type = '普通';
                break;
            case '2':
                $order_type = '定金预售';
                break;
            case '4':
                $order_type = '全款预售';
                break;
            case '5':
                $order_type = '换货补发';
                break;
            default:
                $order_type = $tidinfo['data']['orderType'];
        }

        //$order_status = $tidinfo['data']['orderStatus']; //订单状态
        switch ($tidinfo['data']['orderStatus']) {
            case '1':
                // 执行条件1的操作
                $order_status = '已下单待付款';
                break;
            case '2':
                $order_status = '已支付处理中';
                break;
            case '3':
                $order_status = '清关中';
                break;
            case '4':
                $order_status = '待发货';
                break;
            case '5':
                $order_status = '部分发货';
                break;
            case '6':
                $order_status = '待收货';
                break;
            case '7':
                $order_status = '已完成';
                break;
            case '8':
                $order_status = '已关闭';
                break;
            case '9':
                $order_status = '已取消';
                break;
            case '10':
                $order_status = '换货申请中';
                break;
            default:
                $order_status = $tidinfo['data']['orderStatus'];
        }

        //$order_aftersales_status = $tidinfo['data']['orderAfterSalesStatus'];  //售后状态
        switch ($tidinfo['data']['orderAfterSalesStatus']) {
            case '1':
                // 执行条件1的操作
                $order_aftersales_status = '无售后';
                break;
            case '2':
                $order_aftersales_status = '售后处理中';
                break;
            case '3':
                $order_aftersales_status = '售后完成(含取消)';
                break;
            case '4':
                $order_aftersales_status = '售后拒绝';
                break;
            case '5':
                $order_aftersales_status = '售后关闭';
                break;
            case '6':
                $order_aftersales_status = '平台介入中';
                break;
            default:
                $order_aftersales_status = $tidinfo['data']['orderAfterSalesStatus'];
        }

        $cancel_status = $tidinfo['data']['cancelStatus'];  //申请取消状态
        if($cancel_status == 0){
            $cancel_status = '未申请取消';
        }else{
            $cancel_status = '取消处理中';
        }

        $created_time = date('Y-m-d H:i:s',substr($tidinfo['data']['createdTime'], 0, 10));  //创建时间
        $paid_time = date('Y-m-d H:i:s',substr($tidinfo['data']['paidTime'], 0, 10));  //支付时间
        $payment = $tidinfo['data']['totalPayAmount']/100;  //支付金额

        //根据传参进行操作
        if($or == 'inset'){
            //插入数据
            xhs_tid::create([
                'tid'  =>  $tid,
                'order_type'  =>  $order_type,
                'order_status'  =>  $order_status,
                'order_aftersales_status'  =>  $order_aftersales_status,
                'cancel_status'  =>  $cancel_status,
                'created_time'  =>  $created_time,
                'paid_time'  =>  $paid_time,
                'payment'  =>  $payment
            ]);
        }else{
            //更新数据
            xhs_tid::update([
                'order_type'  =>  $order_type,
                'order_status'  =>  $order_status,
                'order_aftersales_status'  =>  $order_aftersales_status,
                'cancel_status'  =>  $cancel_status
            ],['tid'=>$tid]);
        }




        /*
        echo $tid.'<br>';
        echo $order_type.'<br>';
        echo $order_status.'<br>';
        echo $order_aftersales_status.'<br>';
        echo $cancel_status.'<br>';
        echo $created_time.'<br>';
        echo $paid_time.'<br>';
        echo $payment.'<br>';*/

    }

    //获取订单金额
    public function pullTidPayment($tid) {

        $Xiaohongshu = new Xiaohongshuclass;
        $tidinfo = $Xiaohongshu->getOrderInfo($tid);
        $tid = $tidinfo['data']['orderId'];  //订单号
        $payment = $tidinfo['data']['totalPayAmount']/100;  //支付金额

       /*
        //更新数据
        xhs_tid::update([
            'payment'  =>  $payment
        ],['tid'=>$tid]);
       */

        /*
        echo $tid.'<br>';
        echo $payment.'<br>';*/

        return $payment;

    }

    //数据库写入订单金额
    public function setTidPayment() {

        //$this->pullTidPayment('P717576419765089531');
        $result = xhs_tid::where('t', '0')->select();
        foreach ($result as $row) {
            // 代码逻辑，可以使用 $row 访问每一行数据
            //echo '<pre>';
            //print_r($row->toArray());
            //echo $row->toArray()['tid'];
            $res_payment = $this->pullTidPayment($row->toArray()['tid']);
            echo $row->toArray()['tid'];
            /*
            //更新数据
            xhs_tid::update([
                'payment'  =>  $res_payment,
                't'  =>  1
            ],['tid'=>$row->toArray()['tid']]);*/
        }
    }

}