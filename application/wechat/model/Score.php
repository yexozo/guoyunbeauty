<?php
namespace app\wechat\model;

use think\Model;

class Score extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = '11_jingcai_lk';

    // 设置当前模型的数据库连接
    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 服务器地址
        'hostname'    => '127.0.0.1',
        // 数据库名
        'database'    => 'weixin',
        // 数据库用户名
        'username'    => 'root',
        // 数据库密码
        'password'    => '2eec8a8f0f4129a6',
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => '',
        // 数据库调试模式
        'debug'       => false,
    ];


    /*
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];*/

}