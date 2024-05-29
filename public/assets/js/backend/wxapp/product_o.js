define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'wxapp/product/index' + location.search,
                    add_url: 'wxapp/product/add',
                    edit_url: 'wxapp/product/edit',
                    del_url: 'wxapp/product/del',
                    multi_url: 'wxapp/product/multi',
                    import_url: 'wxapp/product/import',
                    table: 'wxapp_product',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'product', title: __('Product'), operate: 'LIKE'},
                        {field: 'pimage', title: __('Pimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'content_image', title: __('Content_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'payment', title: __('Payment'), operate:'BETWEEN'},
                        {field: 'score', title: __('Score')},
                        {field: 'skuid', title: __('Skuid'), operate: 'LIKE'},
                        {field: 'iid', title: __('Iid'), operate: 'LIKE'},
                        {field: 'qty', title: __('Qty')},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'limitation_quantum', title: __('Limitation_quantum')},
                        {field: 'onlinetime', title: __('Onlinetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'offlinetime', title: __('Offlinetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'siteswitch', title: __('Siteswitch'), table: table, formatter: Table.api.formatter.toggle},
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
