<?php

namespace app\admin\model;

use think\Model;


class Hahaha extends Model
{

    

    

    // 表名
    protected $name = 'hahaha';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'levellist_text'
    ];
    

    
    public function getLevellistList()
    {
        return ['普通用户' => __('普通用户'), '长史会员' => __('长史会员'), '掌事会员' => __('掌事会员'), '司妆会员' => __('司妆会员'), '君合会员' => __('君合会员')];
    }


    public function getLevellistTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['levellist']) ? $data['levellist'] : '');
        $valueArr = explode(',', $value);
        $list = $this->getLevellistList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }

    protected function setLevellistAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }


}
