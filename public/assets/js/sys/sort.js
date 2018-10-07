var base_url = 'http://test.uckendo.com';
layui.use(['table', 'element', 'form', 'tree'], function () {
    var table = layui.table;
    var form = layui.form;
    var element = layui.element;
    //监听表格复选框选择
    table.on('checkbox(demo)', function (obj) {
        console.log(obj)
    });
    //监听工具条
    table.on('tool(demo)', function (obj) {
        var data = obj.data;
        if (obj.event === 'edit') {
            saveSort(data.ticket_id);
        }
    });
    var $ = layui.$, active = {

    };

    $('.demoTable .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    function saveSort(ticket_id) {
        var title = '修改彩票';
        $.get(base_url + '/sys/ticket/update?ticket_id=' + ticket_id, null, function (result) {
            if (result.status != undefined && !result.status) {
                layer.alert(result.message, {icon: 2});
            }
            layer.open({
                type: 1,
                title: title,
                content: result.message,
                btn: ['保存', '取消'],
                shade: false,
                offset: ['50px', '30%'],
                area: ['400px', '350px'],
                zIndex: 10,
                maxmin: true,
                yes: function () {
                    //触发表单的提交事件
                    $('form.sortObj').find('button[lay-filter=edit]').click();
                }, full: function (elem) {
                    var win = window.top === window.self ? window : parent.window;
                    $(win).on('resize', function () {
                        var $this = $(this);
                        elem.width($this.width()).height($this.height()).css({
                            top: 0,
                            left: 0
                        });
                        elem.children('div.layui-layer-content').height($this.height() - 95);
                    });
                }, success: function (layero, index) {
                    //弹出窗口成功后渲染表单
                    var form = layui.form;
                    form.render();
                    form.on('submit(edit)', function (data) {
                        console.log(data.elem); //被执行事件的元素DOM对象，一般为button对象
                        console.log(data.form); //被执行提交的form对象，一般在存在form标签时才会返回
                        console.log(data.field); //当前容器的全部表单字段，名值对形式：{name: value}
                        $.ajax({
                            type: 'POST',
                            url: base_url + '/sys/ticket/update',
                            data: $("form").serialize(),
                            dataType: 'json',
                            success: function (callback) {
                                if (callback.status) {
                                    layer.msg(callback.message, {
                                        icon: 1,
                                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                    }, function () {
                                        layer.close(index);
                                        table.reload('sortListForm', {
                                            where: {
                                                sort_status:1,
                                            },
                                            page: {curr: 1}
                                        });
                                    });
                                } else {
                                    layer.alert(callback.message, {icon: 2});
                                }
                            }
                        });
                        //这里可以写ajax方法提交表单
                        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
                    });
                },
                end: function () {
                    addBoxIndex = -1;
                }
            });
        }, 'JSON');
    }
});