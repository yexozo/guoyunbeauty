<?php
namespace app\wechat\model;

use think\Model;

class Wx_crm extends Model
{

    // 表名
    protected $name = 'wx_crm';
    protected $autoWriteTimestamp = 'datetime';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

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