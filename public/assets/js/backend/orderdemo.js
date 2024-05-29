define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'orderdemo/index' + location.search,
                    add_url: 'orderdemo/add',
                    edit_url: 'orderdemo/edit',
                    del_url: 'orderdemo/del',
                    multi_url: 'orderdemo/multi',
                    import_url: 'orderdemo/import',
                    table: 'wxapp_order',
                }
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
                        {field: 'id', title: __('Id')},
                        {field: 'tid', title: __('Tid'), operate: 'LIKE'},
                        {field: 'openid', title: __('Openid'), operate: 'LIKE'},
                        {field: 'unionid', title: __('Unionid'), operate: 'LIKE'},
                        {field: 'product', title: __('Product'), operate: 'LIKE'},
                        {field: 'pd_iid', title: __('Pd_iid')},
                        {field: 'skuid', title: __('Skuid'), operate: 'LIKE'},
                        {field: 'iid', title: __('Iid'), operate: 'LIKE'},
                        {field: 'payment', title: __('Payment'), operate:'BETWEEN'},
                        {field: 'score', title: __('Score')},
                        {field: 'quantum', title: __('Quantum')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'paytime', title: __('Paytime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'pid', title: __('Pid'), operate: 'LIKE'},
                        {field: 'kid', title: __('Kid'), operate: 'LIKE'},
                        {field: 'kname', title: __('Kname'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"待支付":__('待支付'),"待发货":__('待发货'),"已发货":__('已发货'),"已完成":__('已完成'),"已关闭":__('已关闭')}, formatter: Table.api.formatter.status},
                        {field: 'areainfo.phone', title: __('Areainfo.phone'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
