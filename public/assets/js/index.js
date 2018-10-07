var tab;
var base_url = 'http://cicms.com';
layui.config({
    base: 'http://cicms.com/assets/js/',
    version: new Date().getTime()
}).use(['element', 'layer', 'navbar', 'tab'], function () {
    var element = layui.element,
        $ = layui.jquery,
        layer = layui.layer,
        navbar = layui.navbar();
    tab = layui.tab({
        elem: '.admin-nav-card', //设置选项卡容器
        /*
        maxSetting: {
            max: 5,
            tipMsg: '只能开5个哇，不能再开了。真的。'
        },
        */
        contextMenu: true,
        onSwitch: function (data) { //切换tab
            console.log(data.id); //当前Tab的Id
            console.log(data.index); //得到当前Tab的所在下标
            console.log(data.elem); //得到当前的Tab大容器
            console.log(tab.getCurrentTabId())
        },
        closeBefore: function (obj) {//tab 关闭之前触发的事件
            console.log(obj);
            layer.confirm('确定要关闭' + obj.title + '吗?', {icon: 3, title: '系统提示'}, function (index) {
                //因为confirm是非阻塞的，所以这里关闭当前tab需要调用一下deleteTab方法
                tab.deleteTab(obj.tabId);
                layer.close(index);
            });
            return false;
        }
    });
    //iframe自适应
    $(window).on('resize', function () {
        var $content = $('.admin-nav-card .layui-tab-content');
        $content.height($(this).height() - 147);
        $content.find('iframe').each(function () {
            $(this).height($content.height());
        });
    }).resize();

    //设置navbar
    navbar.set({
        spreadOne: true,
        elem: '#admin-navbar-side',
        cached: true,
        data: navs,
        /*cached:true,
         url: 'datas/nav.json'*/
    });
    //渲染navbar
    navbar.render();
    //监听点击事件
    navbar.on('click(side)', function (data) {
        tab.tabAdd(data.field);
    });
    //清除缓存
    $('#clearCached').on('click', function () {
        // navbar.cleanCached();
        $.getJSON('/sys/menu/clean_cache', null, function (callback) {
            if (callback.status) {
                layer.alert('刷新重载菜单!', {icon: 1, title: '系统提示'}, function () {
                    location.reload();//刷新
                });
            } else {
                layer.alert(callback.message, {icon: 2});
            }
        }, 'json');
    });

    $('.admin-side-toggle').on('click', function () {
        var sideWidth = $('#admin-side').width();
        if (sideWidth === 200) {
            $('#admin-body').animate({
                left: '0'
            }); //admin-footer
            $('#admin-footer').animate({
                left: '0'
            });
            $('#admin-side').animate({
                width: '0'
            });
        } else {
            $('#admin-body').animate({
                left: '200px'
            });
            $('#admin-footer').animate({
                left: '200px'
            });
            $('#admin-side').animate({
                width: '200px'
            });
        }
    });
    $('.admin-side-full').on('click', function () {
        var docElm = document.documentElement;
        //W3C
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen();
        }
        //FireFox
        else if (docElm.mozRequestFullScreen) {
            docElm.mozRequestFullScreen();
        }
        //Chrome等
        else if (docElm.webkitRequestFullScreen) {
            docElm.webkitRequestFullScreen();
        }
        //IE11
        else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
        layer.msg('按Esc即可退出全屏');
    });

    $('#setting').on('click', function () {
        tab.tabAdd({
            href: '/Manage/Account/Setting/',
            icon: 'fa-gear',
            title: '设置'
        });
    });

    //锁屏
    $('#lock').on('click', function () {
        lock($, layer);
    });

    //手机设备的简单适配
    var treeMobile = $('.site-tree-mobile'),
        shadeMobile = $('.site-mobile-shade');
    treeMobile.on('click', function () {
        $('body').addClass('site-mobile');
    });
    shadeMobile.on('click', function () {
        $('body').removeClass('site-mobile');
    });
});

var isShowLock = false;

function lock($, layer) {
    if (isShowLock)
        return;
    //自定页
    var content = $('#lock-temp').html();
    content = content.replace('code_url', base_url + '/front/captcha?' + Math.random());
    layer.open({
        title: false,
        type: 1,
        closeBtn: 0,
        anim: 6,
        content: content,
        shade: [0.9, '#393D49'],
        success: function (layero, lockIndex) {
            isShowLock = true;
            //给显示用户名赋值
            // layero.find('div#lockUserName').text('admin');
            // layero.find('input[name=username]').val('admin');
            layero.find('input[name=password]').on('focus', function () {
                var $this = $(this);
                if ($this.val() === '输入密码解锁..') {
                    $this.val('').attr('type', 'password');
                }
            })
                .on('blur', function () {
                    var $this = $(this);
                    if ($this.val() === '' || $this.length === 0) {
                        $this.attr('type', 'text').val('输入密码解锁..');
                    }
                });
            $.getJSON('/logout', null, function (res) {
                if (!res.rel) {
                    layer.msg(res.msg);
                }
            }, 'json');

            //绑定解锁按钮的点击事件
            layero.find('button#unlock').on('click', function () {
                var userName = $('#lockUserName').html();
                var pwd = $('#lockPwd').val();
                var code = $('#captchacode').val();
                if (pwd === '输入密码解锁..' || pwd.length === 0) {
                    layer.msg('请输入密码..', {
                        icon: 2,
                        time: 1000
                    });
                    return;
                }
                if (code === '输入验证码..' || code.length === 0) {
                    layer.msg('输入验证码..', {
                        icon: 2,
                        time: 1000
                    });
                    return;
                }
                unlock(userName, pwd, code);
            });
            layero.find('img#captchacodeimg').on('click', function () {
                $('#captchacodeimg').attr('src', base_url + '/front/captcha?' + Math.random())
            });
            /**
             * 解锁操作方法
             * @param un    {String} 用户名
             * @param pwd   {String} 密码
             * @param code  {String} 验证码
             */
            var unlock = function (un, pwd, code) {
                console.log(un, pwd);
                //这里可以使用ajax方法解锁
                $.post('/login?' + Math.random(), {loginname: un, loginpass: pwd, captchacode: code}, function (res) {
                    //验证成功
                    if (res.status) {
                        //关闭锁屏层
                        layer.close(lockIndex);
                        isShowLock = false;
                        layer.msg(res.message, {icon: 1, time: 100});
                    } else {
                        layer.msg(res.message, {icon: 2, time: 1000});
                    }
                }, 'json');
            };
        }
    });
    return;
};
