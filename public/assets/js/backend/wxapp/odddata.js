define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'wxapp/odddata/index' + location.search,
                    add_url: 'wxapp/odddata/add',
                    edit_url: 'wxapp/odddata/edit',
                    del_url: 'wxapp/odddata/del',
                    multi_url: 'wxapp/odddata/multi',
                    import_url: 'wxapp/odddata/import',
                    table: 'wxapp_order',
                },
                searchFormVisible: true
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'product', title: __('Product'), operate: 'like'},
                        {field: 'pd_iid', title: __('Pd_iid'),operate: false},
                        {field: 'quantity', title: __('订单累计数量'), operate: false},
                        {field: 'total_amount', title: __('订单累计金额'), operate: false},
                        {field: 'total_score', title: __('订单所消耗积分'), operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false,visible:false},
                        //{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
