var base_url = 'http://cicms.com';
layui.use(['table', 'form','tree'], function () {
    var table = layui.table;
    var form = layui.form;
    //监听表格复选框选择
    table.on('checkbox(demo)', function (obj) {
        console.log(obj)
    });
    //监听工具条
    table.on('tool(demo)', function (obj) {
        var data = obj.data;
        if (obj.event === 'detail') {
            layer.msg('ID：' + data.id + ' 的查看操作');
        } else if (obj.event === 'del') {
            layer.confirm('确认删除该行？', function (index) {
                $.getJSON(base_url + '/role/disable', {role_id: data.role_id}, function (res) {
                    layer.close(index);
                    if (res.status) {
                        layer.msg(res.message, {icon: 1, time: 2000});
                        obj.del();
                    } else {
                        layer.alert(res.message, {icon: 2});
                    }
                });

            });
        } else if (obj.event === 'edit') {
            saveRole('update', data.role_id);
        } else if(obj.event === 'config'){
            saveAccess(data.role_id);
        }
    });
    var $ = layui.$, active = {
        getCheckData: function () { //获取选中数据
            var checkStatus = table.checkStatus('roleListForm')
                , data = checkStatus.data;
            layer.open({
                type: 1,
                title: '信息',
                content: JSON.stringify(data),
                btn: '关闭',
                shade: false,
                area: ['600px', '400px'],
                maxmin: true
            });

        }, searchData: function () {
            table.reload('roleListForm', {
                where: {
                    role_name: $('#role_name').val()
                }
            });
        }, addRole: function (form) {
            var addBoxIndex = -1;
            saveRole('create');
        }
    };

    $('.demoTable .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    /**
     * 角色弹出层
     *
     * @param type
     * @param role_id
     */
    function saveRole(type, role_id) {
        var title = '新增菜单';
        var url = '';
        if (type == 'update') {
            title = '更新角色';
            url += '?role_id=' + role_id;
        }
        $.get(base_url + '/sys/role/' + type + url, null, function (form) {
            layer.open({
                type: 1,
                title: title,
                content: form,
                btn: ['保存', '取消'],
                shade: false,
                offset: ['50px', '30%'],
                area: ['400px', '350px'],
                zIndex: 10,
                maxmin: true,
                yes: function () {
                    //触发表单的提交事件
                    $('form.layui-form').find('button[lay-filter=edit]').click();
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
                            url: base_url + '/sys/role/' + type,
                            data: $("form").serialize(),
                            dataType: 'json',
                            success: function (callback) {
                                if (callback.status) {
                                    layer.msg(callback.message, {
                                        icon: 1,
                                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                    }, function () {
                                        layer.close(index);
                                        location.reload();//刷新
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
        });
    }

    function saveAccess(role_id) {
        var title = '权限控制';
        $.get(base_url + '/sys/role/set_access?role_id='+role_id, null, function (result) {
            if(result.status != undefined && !result.status){
                layer.alert(result.message, {icon: 2});
            }
            layer.open({
                type: 1,
                title: title,
                content: result.data.accessList,
                btn: ['保存', '取消'],
                shade: false,
                offset: ['50px', '30%'],
                area: ['400px', '350px'],
                zIndex: 10,
                maxmin: true,
                yes: function () {
                    //触发表单的提交事件
                    $('form.accessList').find('button[lay-filter=edit]').click();
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
                            url: base_url + '/sys/role/set_access',
                            data: $("form").serialize(),
                            dataType: 'json',
                            success: function (callback) {
                                if (callback.status) {
                                    layer.msg(callback.message, {
                                        icon: 1,
                                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                    }, function () {
                                        layer.close(index);
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
        },'JSON');
    }

});