define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'hahaha/index' + location.search,
                    add_url: 'hahaha/add',
                    edit_url: 'hahaha/edit',
                    del_url: 'hahaha/del',
                    multi_url: 'hahaha/multi',
                    import_url: 'hahaha/import',
                    table: 'hahaha',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'levellist', title: __('Levellist'), searchList: {"普通用户":__('普通用户'),"长史会员":__('长史会员'),"掌事会员":__('掌事会员'),"司妆会员":__('司妆会员'),"君合会员":__('君合会员')}, operate:'FIND_IN_SET', formatter: Table.api.formatter.label},
                        {field: 'phone', title: __('Phone')},
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
