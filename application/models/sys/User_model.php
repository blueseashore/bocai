<?php

/**
 * 系统用户模型
 *
 * User: kendo
 */
class User_model extends CI_Model
{
    private $_table = 'sys_user';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->lang->load('user');
        $this->load->database();
    }

    /**
     * 根据用户ID获取用户信息
     *
     * @param int $user_id
     * @return array
     * @throws Exception
     */
    public function get($user_id = 0)
    {
        $user = $this->db->get_where($this->_table, ['user_id' => $user_id])->row_array();
        if (!$user) show_error('请输入正确的用户ID');
        return $user;
    }

    /**
     * 获取用户列表
     *
     * @param array $param
     * @param bool $is_page
     * @param bool $is_array
     * @return string
     */
    public function get_user(array $param, $is_array = TRUE, $is_page = TRUE)
    {
        $this->db->select('user_id,user_name,user_email,display_name,user_status,inet_ntoa(last_ip) last_ip,from_unixtime(last_login) last_login');
        if (!empty($param['search_type']) && isset($param['search_value'])) {
            $this->db->where($param['search_type'], $param['search_value']);
        }
        if ($is_page) {
            $page = !empty($param['page']) ? intval($param['page']) : 1;
            $limit = !empty($param['limit']) ? intval($param['limit']) : 10;
            $this->db->limit($limit, ($page - 1) * $limit);
        }
        $user_list = $this->db->get($this->_table)->result_array();
        if ($is_array) {
            return $user_list;
        } else {
            $result = $this->db->simple_query(filter_limit_sql($this->db->last_query()));
            return send_list_json($user_list, $result->num_rows);
        }
    }

    /**
     * 保存用户信息
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function save_user(array $data)
    {
        $time = time();
        if (isset($data['user_id'])) {
            $this->get($data['user_id']);
        } else {
            $data['update_time'] = $time;
        }
        if (isset($data['user_pass'])) {
            $info['user_pass'] = password_hash($data['user_pass'], PASSWORD_DEFAULT);
        }
        $info['user_name'] = $data['user_name'];
        $info['user_email'] = $data['user_email'];
        $info['display_name'] = $data['display_name'];
        $info['user_status'] = intval(isset($data['user_status']));
        try {
            $this->db->trans_begin();
            if (isset($data['user_id'])) {
                $this->db->update($this->_table, $info, ['user_id' => $data['user_id']]);
            } else {
                $this->db->insert($this->_table, $info);
                $data['user_id'] = $this->db->insert_id();
            }
            if (!empty($data['user_role'])) {
                $this->user_role_model->save_user_role($data['user_id'], $data['user_role']);
            }
            $this->db->trans_commit();
            return TRUE;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            throw new Exception($e->getMessage());
        }
    }


    /**
     * 用户登录
     *
     * @throws Exception
     */
    public function login()
    {
        try {
            $user_data = $this->session->get_userdata();
            $user_name = $this->input->post('loginname');
            $user_pass = $this->input->post('loginpass');
            if (isset($user_data['user_name']) && isset($user_data['expire_time']) && $user_data['expire_time'] >= time()) {
                if ($user_data['user_name'] != $user_name) {
                    throw new Exception($this->lang->line('user_login_already'));
                }
                //延长session的过期时间
                $this->session->set_userdata('expire_time', time() + $this->config->item('sess_expiration'));
            } else {    //正常登录
                $userObj = $this->db->get_where($this->_table, ['user_name' => $user_name])
                    ->row();
                if (empty($userObj)) {
                    throw new Exception($this->lang->line('user_info_error'));
                }
                if (password_verify($user_pass, $userObj->user_pass) === FALSE) {
                    throw new Exception($this->lang->line('user_info_error'));
                } else {
                    $userObj->last_ip = ip2long($_SERVER['REMOTE_ADDR']);
                    $userObj->last_login = time();
                    $this->db->set($userObj);
                    $this->db->update($this->_table, null, ['user_id' => $userObj->user_id]);
                    $user_data = [
                        'user_id' => $userObj->user_id,
                        'user_name' => $userObj->user_name,
                        'display_name' => $userObj->display_name,
                        'expire_time' => time() + $this->config->item('sess_expiration'),
                        'login_ip' => $_SERVER['REMOTE_ADDR'],
                    ];
                    $this->session->set_userdata($user_data);
                    if ($this->input->post('rememberMe') === 'true') {
                        $_COOKIE['loginname'] = $this->input->post('loginname');
                    }
                }
            }
            send_json(TRUE, $this->lang->line('user_login_success'));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 用户登出
     */
    public function logout()
    {
        $this->session->sess_destroy();
        if (IS_AJAX) {
            send_json(TRUE, $this->lang->line('user_logout_success'));
        } else {
            header('location:/login');
        }
    }

    /**
     * 菜单存在检测+访问权限控制
     *
     * @param int $user_id
     * @param string $route
     * @return bool
     * @throws Exception
     */
    public function check_acl($user_id = 0, $route = '')
    {
        $get_menu = 'SELECT menu_id,menu_name FROM sys_menu WHERE menu_status = 1 AND  menu_uri="' . $route . '" OR menu_uri_short = "' . $route . '" LIMIT 1';
        $menu = $this->db->query($get_menu)->row_array();
        if (empty($menu)) {
            throw new Exception($route . '-暂未开放');
        } else {
            $get_role = 'SELECT role_id FROM sys_user_role WHERE user_id = ' . $user_id . ' AND user_role_status = 1';
            $sql = 'SELECT id FROM sys_role_menu WHERE menu_id = ' . $menu['menu_id'] . ' AND role_id IN (' . $get_role . ') LIMIT 1';
            $data = $this->db->query($sql)->row_array();
            if (empty($data)) {
                throw new Exception($menu['menu_name'] . '_暂无访问权限');
            } else {
                return TRUE;
            }
        }
    }
}