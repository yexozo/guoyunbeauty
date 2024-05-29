<?php

namespace app\admin\controller\wxapp;

use app\common\controller\Backend;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * 订单列管理
 *
 * @icon fa fa-circle-o
 */
class Oddlist extends Backend
{

    /**
     * Oddlist模型对象
     * @var \app\admin\model\wxapp\Oddlist
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\wxapp\Oddlist;
        $this->view->assign("statusList", $this->model->getStatusList());
    }


    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['areainfo'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {

                $row->getRelation('areainfo')->visible(['phone']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 导出列表
     */
    public function export()
    {

        if ($this->request->isPost()) {
            set_time_limit(0);
            $search = $this->request->post('search');
            $ids = $this->request->post('ids');
            $filter = $this->request->post('filter');
            $op = $this->request->post('op');
            $columns = $this->request->post('columns');

            //$excel = new PHPExcel();
            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()
                ->setCreator("FastAdmin")
                ->setLastModifiedBy("FastAdmin")
                ->setTitle("标题")
                ->setSubject("Subject");
            $spreadsheet->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(12);

            $worksheet = $spreadsheet->setActiveSheetIndex(0);
            $whereIds = $ids == 'all' ? '1=1' : ['bt_wxapp_order.id' => ['in', explode(',', $ids)]];

            $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $line = 1;

            //设置过滤方法
            $this->request->filter(['strip_tags']);

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                //->with(['areainfo'])
                ->where($where)
                ->where($whereIds)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['areainfo'])
                ->where($where)
                ->where($whereIds)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {
                $row->getRelation('areainfo')->visible(['phone']);
            }

            $list = collection($list)->toArray();

            // 重新排序
            // 遍历数组
            foreach ($list as $key => &$val) {
                // 检查areainfo字段是否存在
                if (isset($val['areainfo'])) {
                    // 提取phone字段的值
                    $phone = $val['areainfo']['phone'];

                    // 删除areainfo字段
                    unset($val['areainfo']);

                    // 如果需要，可以将phone字段添加到数组的其他位置
                    // 例如，作为数组的一个新字段
                    $val['phone'] = $phone;
                }
            }
            // 取消引用，防止意外的修改
            unset($val);


            $result = array("total" => $total, "rows" => $list);

            $first = array_keys($list[0]);


            //echo '<pre>';
            //print_r($first);exit;

            foreach ($first as $index => $item) {
                $worksheet->setCellValueByColumnAndRow($index+1, 1, __($item));  //$index+1 显示id列
            }

            $row = 2; //第二行开始
            foreach ($list as $item) {
                $column = 1; //显示列
                foreach ($item as $value) {
                    $worksheet->setCellValueByColumnAndRow($column, $row, $value);
                    $column++;
                }
                $row++;
            }

            # 保存为xls
            $filename = '订单详情.xls';
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save($filename);
            # 浏览器下载
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        }

    }




    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
