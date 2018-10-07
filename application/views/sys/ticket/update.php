<div style="margin: 15px;">
    <?php validation_errors(); ?>
    <?php echo form_open('sys/sort/update', array('class' => 'sortObj')); ?>
    <input type="hidden" name="ticket_id" value="<?= $sort['ticket_id'] ?>"/>
    <div class="layui-form-item">
        <label class="layui-form-label">角色名称</label>
        <div class="layui-input-block">
            <input type="text" name="name" value="<?= $sort['name'] ?>" placeholder="请输入"
                   autocomplete="off" class="layui-input"
                   lay-verify="required">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
            <div class="layui-input-block">
                <input type="number" name="sort_num" value="<?= $sort['sort_num'] ?>" placeholder="请输入"
                       autocomplete="off"
                       class="layui-input">
            </div>
    </div>
    <button lay-filter="edit" lay-submit style="display: none;"></button>
    </form>
</div>
