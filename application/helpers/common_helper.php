<?php

/**
 * 自定义函数
 *
 * User: kendo
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 获取文字图标
 *
 * @param string $icon
 * @return string
 */
function get_icon_str($icon = '')
{
    if (empty($icon)) return '';
    if (strstr($icon, 'fa')) {
        $menu_icon = '<i class="fa ' . $icon . '" aria-hidden="true" data-icon="' . $icon . '"></i>';
    } else {
        $menu_icon = '<i class="layui-icon" data-icon="' . $icon . '">' . $icon . '</i>';
    }
    return $menu_icon;
}

/**
 * 登出
 */
function logout()
{
    $ci = &load_class('Model', 'core');
    $ci->session->unset_userdata('user_name');
    if (IS_AJAX) {
        send_json(TRUE, 'Success');
    } else {
        header('location:/login');
    }
}

/**
 * 返回JSON
 *
 * @param bool $status
 * @param array $info
 */
function send_json($status = TRUE, $info = [])
{
    $data['status'] = $status;
    $data['message'] = '';
    if (empty($info)) {
        if ($status) {
            $data['message'] = 'Success';
        } else {
            $data['message'] = 'Failure';
        }
    } else {
        if (is_string($info)) {
            $data['message'] = $info;
        } elseif (is_array($info) && !empty($info)) {
            $data['data'] = $info;
        }
    }
    exit(json_encode($data));
}

/**
 * 发送列表页需要的JSON数据
 *
 * @param array $list
 * @param int $total
 * @return string
 */
function send_list_json($list = [], $total = 0)
{
    $data_list['code'] = 0;
    $data_list['rel'] = true;
    $data_list['msg'] = '获取成功';
    $data_list['count'] = $total;
    $data_list['data'] = empty($list) ? [] : $list;
    echo json_encode($data_list);
}

/**
 * 过滤掉 limit 后面的字符
 *
 * @param string $sql
 * @return bool|int|string
 */
function filter_limit_sql($sql = '')
{
//    $sql = strtoupper($sql);
    $pos = strpos($sql, 'LIMIT');
    if ($pos) {
        $sql = substr($sql, 0, $pos);
    }
    return $sql;
}