<?php

namespace app\admin\model\treasure;

use think\Model;


class Idnex extends Model
{

    

    

    // 表名
    protected $name = 'treasure_chest';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'isrepay_status_text'
    ];
    

    
    public function getIsrepayStatusList()
    {
        return ['已回款' => __('已回款'), '待审核' => __('待审核'), '未回款' => __('未回款')];
    }


    public function getIsrepayStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['isrepay_status']) ? $data['isrepay_status'] : '');
        $list = $this->getIsrepayStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
