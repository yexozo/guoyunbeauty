<?php

namespace app\admin\model\wxapp;

use think\Model;


class Product extends Model
{

    

    

    // 表名
    protected $name = 'wxapp_product';
    
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
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }



    public function getLimitation_set()
    {
        return ['不限购' => __('不限购'), '终身限购' => __('终身限购'), '每天' => __('每天'), '每周' => __('每周'), '每月' => __('每月')];
    }
    public function getLimitationSetTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['limitation_set']) ? $data['limitation_set'] : '');
        $list = $this->getLimitation_set();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getLevellistList()
    {
        return ['无限制' => __('无限制'), '普通用户' => __('普通用户'), '长史会员' => __('长史会员'), '掌事会员' => __('掌事会员'), '司妆会员' => __('司妆会员'), '君合会员' => __('君合会员')];
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
