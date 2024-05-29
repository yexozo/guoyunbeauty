<?php
namespace app\wechat\model;

use think\Model;

class Demo extends Model
{
    public function getDataFromTable1()
    {
        // 查询第一个表的数据
        return $this->table('bt_test')->select();
    }

    public function getDataFromTable2()
    {
        // 查询第二个表的数据
        return $this->table('bt_test2')->select();
    }


}