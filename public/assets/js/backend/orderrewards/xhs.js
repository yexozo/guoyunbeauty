define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'orderrewards/xhs/index' + location.search,
                    add_url: 'orderrewards/xhs/add',
                    edit_url: 'orderrewards/xhs/edit',
                    del_url: 'orderrewards/xhs/del',
                    multi_url: 'orderrewards/xhs/multi',
                    import_url: 'orderrewards/xhs/import',
                    table: 'xhs_tid',
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
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'tid', title: __('Tid'), operate: 'LIKE'},
                        {field: 'order_type', title: __('Order_type'), operate: 'LIKE'},
                        {field: 'order_status', title: __('Order_status'), operate: 'LIKE', formatter: Table.api.formatter.status},
                        {field: 'order_aftersales_status', title: __('Order_aftersales_status'), operate: 'LIKE', formatter: Table.api.formatter.status},
                        {field: 'cancel_status', title: __('Cancel_status'), operate: 'LIKE', formatter: Table.api.formatter.status},
                        {field: 'created_time', title: __('Created_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'paid_time', title: __('Paid_time'), operate: false, addclass:'datetimerange', autocomplete:false},//operate:'RANGE'
                        {field: 'payment', title: __('Payment'), operate:'BETWEEN'},
                        //{field: 'd_man', title: __('D_man'), operate: 'LIKE'},
                        //{field: 'd_time', title: __('D_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
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
