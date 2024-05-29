<?php

namespace app\admin\model;

use think\Model;


class Orderdemo extends Model
{

    

    

    // 表名
    protected $name = 'wxapp_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['待支付' => __('待支付'), '待发货' => __('待发货'), '已发货' => __('已发货'), '已完成' => __('已完成'), '已关闭' => __('已关闭')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function areainfo()
    {
        return $this->hasOne('app\admin\model\wxapp\Areainfo', 'unionid', 'unionid', [], 'LEFT')->setEagerlyType(0);
    }
}
