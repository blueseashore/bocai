<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet"
          href="<?= $this->config->item('base_url') ?>/assets/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= $this->config->item('base_url') ?>/assets/css/layui.css" media="all">
</head>
<body style="margin-left: 10px;margin-top: 10px;">
<table class="layui-table"
       lay-data="{height:'full',url:'index', page:true,id:'sortListForm',where:{sort_status:1},limit:10}"
       lay-filter="demo" style="margin-left: 20px;">
    <thead>
    <tr>
        <th lay-data="{checkbox:true, fixed: true}"></th>
        <th lay-data="{field:'ticket_id', width:80, sort: true, fixed: true}">ID</th>
        <th lay-data="{field:'name', width:150}">名称</th>
        <th lay-data="{field:'sort_num', width:150}">排序</th>
        <th lay-data="{fixed: 'right', width:200,toolbar: '#barDemo'}">操作</th>
    </tr>
    </thead>
</table>
<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-mini" lay-event="edit">编辑</a>
</script>

<script src="<?= $this->config->item('base_url') ?>/assets/layui/layui.all.js" charset="utf-8"></script>
<script src="<?= $this->config->item('base_url') ?>/assets/js/sys/sort.js" charset="utf-8"></script>
</body>
</html>