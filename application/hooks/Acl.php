<?php

/**
 * 控制权限类
 *
 * 潮馆
 * User: kendo
 */
class Acl
{
    private $_ci;

    public function acl_control()
    {
        $this->_ci = &get_instance();
        $route = $this->_ci->uri->uri_string();
        if (!empty($route)) {
            $white_list = $this->_ci->config->item('white_list');
            if (!empty($white_list) && in_array(strtolower($route), $white_list)) {
                return TRUE;
            }
            $this->_ci->load->library('session');
            $user_data = $this->_ci->session->get_userdata();
            if (!isset($user_data['user_id'])) return FALSE;
            if ($user_data['user_id'] == 1) return TRUE;
            $this->_ci->load->model('sys/user_model');
            $this->_ci->user_model->check_acl($user_data['user_id'], $route);
        }
    }
}