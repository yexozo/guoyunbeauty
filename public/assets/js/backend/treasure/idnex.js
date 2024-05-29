define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'treasure/idnex/index' + location.search,
                    add_url: 'treasure/idnex/add',
                    edit_url: 'treasure/idnex/edit',
                    del_url: 'treasure/idnex/del',
                    multi_url: 'treasure/idnex/multi',
                    table: 'treasure_chest',
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
                        //{field: 'id', title: __('Id')},
                        //{field: 'openid', title: __('Openid'), operate: 'LIKE'},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'event', title: __('Event'), operate: 'LIKE'},
                        {field: 'points', title: __('Points'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'paytime', title: __('Paytime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'progress', title: __('Progress'), searchList: {"待审核":__('待审核'),"已审核":__('已审核'),"已打款":__('已打款')}, operate:'FIND_IN_SET', formatter: Table.api.formatter.label},
                        {field: 'isrepay_status', title: __('Isrepay_status'), searchList: {"已回款":__('已回款'),"待审核":__('待审核'),"未回款":__('未回款')}, formatter: Table.api.formatter.status},
                        //操作栏,默认有编辑、删除或排序按钮,可自定义配置buttons来扩展按钮
                        {
                            field: 'operate',
                            width: "150px",
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                /*
                                {
                                    name: 'click',
                                    title: __('点击执行事件'),
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data) {
                                        Layer.alert("点击按钮执行的事件");
                                    }
                                },
                                {
                                    name: 'detail',
                                    title: __('弹出窗口打开'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: 'treasure/idnex/detail',
                                    callback: function (data) {
                                        Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
                                    }
                                },*/
                                {
                                    name: 'checkout',
                                    title: __('打款'),
                                    classname: 'btn btn-xs btn-warning btn-magic btn-ajax',
                                    icon: 'fa fa-check-square',
                                    confirm: '是否确认已打款',
                                    url: 'treasure/idnex/checkout',
                                    success: function (data, ret) {
                                        //table.bootstrapTable('updateCell', {field: "name", value: '改了', index: 0})

                                        $(".btn-refresh").click();
                                        //console.log(data)


                                        //更新表格上方汇总数据
                                        //var test1 = $('#test1').html();
                                        //alert(test1);


                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'ajax',
                                    title: __('审核'),
                                    classname: 'btn btn-xs btn-info btn-magic btn-ajax',
                                    icon: 'fa fa-star',
                                    confirm: '是否通过审核',
                                    url: 'treasure/idnex/ajax',
                                    success: function (data, ret) {
                                        //table.bootstrapTable('updateCell', {field: "name", value: '改了', index: 0})

                                        $(".btn-refresh").click();
                                        //console.log(data)


                                        //更新表格上方汇总数据
                                        //var test1 = $('#test1').html();
                                        //alert(test1);


                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                }/*,
                                {
                                    name: 'addtabs',
                                    title: __('新选项卡中打开'),
                                    classname: 'btn btn-xs btn-warning btn-addtabs',
                                    icon: 'fa fa-folder-o',
                                    url: 'example/bootstraptable/detail'
                                }*/
                            ],

                            formatter: function (value, row, index) { //隐藏自定义的视频按钮
                                var that = $.extend({}, this);
                                var table = $(that.table).clone(true);
                                //权限判断
                                if(Config.checkout !== true){  //通过Config.chapter 获取后台存的chapter
                                    console.log('没有权限');
                                    $(table).data("operate-checkout", null);
                                    that.table = table;
                                }else{
                                    console.log('有权限');
                                }
                                return Table.api.formatter.operate.call(that, value, row, index);
                            }
                        }
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
        detail: function () {
            $(document).on('click', '.btn-callback', function () {
                Fast.api.close($("input[name=callback]").val());
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
