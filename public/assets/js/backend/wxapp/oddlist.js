define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'wxapp/oddlist/index' + location.search,
                    add_url: '',
                    edit_url: '',
                    del_url: 'wxapp/oddlist/del',
                    multi_url: '',
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
                        //{field: 'openid', title: __('Openid'), operate: 'LIKE'},
                        {field: 'unionid', title: __('Unionid'), operate: 'LIKE'},
                        {field: 'areainfo.phone', title: __('Areainfo.phone'), operate: 'LIKE'},
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
                        {field: 'status', title: __('Status'), searchList: {"待支付":__('待支付'),"待发货":__('待发货'),"已发货":__('已发货'),"已完成":__('已完成'),"已关闭":__('已关闭')}, formatter: Table.api.formatter.status}/*,
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}*/
                    ]
                ]
            });




            var submitForm = function (ids, layero) {
                var options = table.bootstrapTable('getOptions');
                console.log(options);
                var columns = [];
                $.each(options.columns[0], function (i, j) {
                    if (j.field && !j.checkbox && j.visible && j.field != 'operate') {
                        columns.push(j.field);
                    }
                });
                var search = options.queryParams({});
                $("input[name=search]", layero).val(options.searchText);
                $("input[name=ids]", layero).val(ids);
                $("input[name=filter]", layero).val(search.filter);
                $("input[name=op]", layero).val(search.op);
                $("input[name=columns]", layero).val(columns.join(','));
                $("form", layero).submit();
            };
            $(document).on("click", ".btn-export", function () {
                var ids = Table.api.selectedids(table);
                var page = table.bootstrapTable('getData');
                var all = table.bootstrapTable('getOptions').totalRows;
                console.log(ids, page, all);
                Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl("wxapp/oddlist/export") + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
                    title: '导出数据',
                    btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)", "全部(" + all + "条)"],
                    success: function (layero, index) {
                        $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                    }
                    , yes: function (index, layero) {
                        submitForm(ids.join(","), layero);
                        return false;
                    }
                    ,
                    btn2: function (index, layero) {
                        var ids = [];
                        $.each(page, function (i, j) {
                            ids.push(j.id);
                        });
                        submitForm(ids.join(","), layero);
                        return false;
                    }
                    ,
                    btn3: function (index, layero) {
                        submitForm("all", layero);
                        return false;
                    }
                })
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


