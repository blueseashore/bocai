</div>
</div>
</div>
<div class="layui-footer footer footer-demo" id="admin-footer">
    <div class="layui-main">
        <p><?= date('Y') ?> &copy;
            <a href="#">Uckendo</a> LGPL license 更多模板,请联系QQ：455019825
        </p>
    </div>
</div>
<div class="site-tree-mobile layui-hide">
    <i class="layui-icon">&#xe602;</i>
</div>
<div class="site-mobile-shade"></div>
</div>
</div>
<!--锁屏模板 start-->
<script type="text/template" id="lock-temp">
    <div class="admin-header-lock" id="lock-box">
        <div class="admin-header-lock-img">
            <img src="<?= $this->config->item('base_url') ?>/assets/images/0.jpg"/>
        </div>
        <div class="admin-header-lock-name" id="lockUserName"><?= $this->session->get_userdata()['user_name']; ?></div>
        <input type="text" class="admin-header-lock-input" value="" placeholder="输入验证码.." name="captchacode"
               id="captchacode" style="width: 94px;height: 38px;"/>
        <img id="captchacodeimg" src="code_url">
        <input type="password" class="admin-header-lock-input" value="" placeholder="输入密码解锁.." name="lockPwd"
               id="lockPwd" style="height: 38px;"/>
        <button class="layui-btn layui-btn-small" id="unlock">解锁</button>
    </div>
</script>
<!--锁屏模板 end -->
<script type="text/javascript" src="<?= $this->config->item('base_url') ?>assets/plugins/layui/layui.js"></script>
<script>
    <?php
    $param = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    ?>
    var navs = <?=file_get_contents($this->config->item('base_url') . 'assets/menu.json', FALSE, stream_context_create($param))?>;
</script>
<script src="<?= $this->config->item('base_url') ?>assets/js/index.js"></script>
</body>
</html>