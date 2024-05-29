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
                        {field: 'exchangenum', title: __('Exchangenum')},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'limitation_set', title: __('Limitation_set'), searchList: {"不限购":__('不限购'),"终身限购":__('终身限购'),"每天":__('每天'),"每周":__('每周'),"每月":__('每月')}, operate:'FIND_IN_SET', formatter: Table.api.formatter.label},
                        {field: 'limitation_quantum', title: __('Limitation_quantum')},
                        {field: 'levellist', title: __('Levellist'), searchList: {"无限制":__('无限制'),"普通用户":__('普通用户'),"长史会员":__('长史会员'),"掌事会员":__('掌事会员'),"司妆会员":__('司妆会员'),"君合会员":__('君合会员')}, operate:'FIND_IN_SET', formatter: Table.api.formatter.label},
                        //{field: 'levellist', title: __('Levellist'), searchList: {"普通用户":__('普通用户'),"长史会员":__('长史会员'),"掌事会员":__('掌事会员'),"司妆会员":__('司妆会员'),"君合会员":__('君合会员')}},
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

            //限购设置，当用户选择“不限购” 则隐藏填写限购量的input框，并设置限购量为0
            $("#c-limitation_set").change(function() {
                // 获取用户选择的值
                var selectedValue = $(this).val();
                // 根据选择的值进行相应的处理
                if (selectedValue !== "不限购") {
                    // 当选择了"终身限购"时，处理逻辑
                    $('#a-limitation_quantum').css('display','block');

                }else{
                    $('#c-limitation_quantum').val('0');
                    $('#a-limitation_quantum').css('display','none');
                }
            });


        },
        edit: function () {
            Controller.api.bindevent();

            //限购设置，当用户选择“不限购” 则隐藏填写限购量的input框，并设置限购量为0
            var c_e_lim_quantum = $('#c-limitation_quantum').val();
            if(c_e_lim_quantum == 0){
                $('#a-limitation_quantum').css('display','none');
            }
            $("#c-limitation_set").change(function() {
                // 获取用户选择的值
                var selectedValue = $(this).val();
                // 根据选择的值进行相应的处理
                if (selectedValue !== "不限购") {
                    // 当选择了"终身限购"时，处理逻辑
                    $('#a-limitation_quantum').css('display','block');

                }else{
                    $('#c-limitation_quantum').val('0');
                    $('#a-limitation_quantum').css('display','none');
                }
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


