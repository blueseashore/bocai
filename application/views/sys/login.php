<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= $this->config->item('base_url') ?>assets/plugins/layui/css/layui.css"
          media="all"/>
    <link rel="stylesheet" href="<?= $this->config->item('base_url') ?>assets/css/login.css"/>
</head>

<body class="beg-login-bg">
<div class="beg-login-box">
    <header>
        <h1><?= $title ?></h1>
    </header>
    <div class="beg-login-main" style="height: 250px;">
        <?php echo validation_errors() ?>
        <?php echo form_open('', array('class' => 'layui-form', 'method' => 'post')); ?>
        <div class="layui-form-item">
            <label class="beg-login-icon">
                <i class="layui-icon">&#xe612;</i>
            </label>
            <input type="text" name="loginname" lay-verify="loginname" autocomplete="off" placeholder="请输入登录名"
                   class="layui-input" required>
        </div>
        <div class="layui-form-item">
            <label class="beg-login-icon">
                <i class="layui-icon">&#xe642;</i>
            </label>
            <input type="password" name="loginpass" lay-verify="loginpass" autocomplete="off" placeholder="请输入密码"
                   class="layui-input" required>
        </div>
        <div class="layui-form-item">
        <div class="layui-form-item">
            <div class="beg-pull-left beg-login-remember">
                <label>记住帐号？</label>
                <input type="checkbox" name="rememberMe" value="true" lay-skin="switch" checked title="记住帐号">
            </div>
            <div class="beg-pull-right">
                <button class="layui-btn layui-btn-primary" lay-submit lay-filter="login">
                    <i class="layui-icon">&#xe650;</i> 登录
                </button>
            </div>
            <div class="beg-clear"></div>
        </div>
        </form>
    </div>
    <footer>
        <p>后台管理 © www.test.uckendo.com</p>
    </footer>
</div>
<script type="text/javascript" src="<?= $this->config->item('base_url') ?>assets/plugins/layui/layui.js"></script>
<script>
    layui.use(['layer', 'form', 'jquery'], function () {
        var layer = layui.layer,
            $ = layui.jquery,
            form = layui.form();
        $('#captchacode').on('click', function () {
            $('#captchacode').attr('src', '<?=$this->config->item('base_url')?>front/captcha?' + Math.random());
        });
        form.on('submit(login)', function () {
            $.ajax({
                type: 'post',
                data: $("form").serialize(),
                url: '/login?'+Math.random(),
                dataType: 'json',
                success: function (callback) {
                    if (callback.status) {
                        layer.msg(callback.message, {
                            icon: 1,
                            time: 1 //2秒关闭（如果不配置，默认是3秒）
                        }, function () {
                            window.location.href = '<?=$this->config->item('base_url')?>';
                        });
                        layer.close(index);
                    } else {
                        layer.alert(callback.message, {icon: 2});
                    }
                }
            });
            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });
    });
</script>
</body>
</html>