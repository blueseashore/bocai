<?php

/**
 * 用户管理
 *
 * User: kendo
 */
class User extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('sys/user_model');
        $this->load->model('sys/role_model');
        $this->load->model('sys/user_role_model');
    }

    private $user_level = [
        '普通账户',         //需要通过角色进行授权
        '只读账户',         //除特殊模块外，只有读权限，无增删改查上传功能
        '读写账户',         //除特殊模块外，读写权限全开
        '超级管理员',       //权限全开
    ];

    /**
     * 登录页面
     */
    public function login()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('loginname', 'LoginName', 'required');
        $this->form_validation->set_rules('loginpass', 'LoginPassword', 'required');
        if ($this->form_validation->run() === FALSE) {
            $data['title'] = '登录页面';
            $this->load->view('sys/login', $data);
        } else {
            try {
                $this->user_model->login();
                send_json(TRUE, $this->lang->line('user_login_success'));
            } catch (Exception $e) {
                send_json(FALSE, $e->getMessage());
            }
        }
    }

    /**
     * 表单验证
     *  $user_level = [
     * '普通账户',         //需要通过角色进行授权
     * '只读账户',         //除特殊模块外，只有读权限，无增删改查上传功能
     * '读写账户',         //除特殊模块外，读写权限全开
     * '超级管理员',       //权限全开
     * ];
     * @param string $type
     * @throws Exception
     */
    private function __formValidation($type = 'create')
    {
        $this->load->library('form_validation');
        if ($type === 'create') {
            $name_error = [
                'is_unique' => "用户名已经存在,请更换",
            ];
            $this->form_validation->set_rules('user_name', 'UserName', 'trim|required|is_unique[sys_user.user_name]', $name_error);
            $this->form_validation->set_rules('user_pass', 'UserPassword', 'trim|required');
            $pass_error = [
                'matches' => "第二次输入的密码与第一次输入的密码不相同."
            ];
            $this->form_validation->set_rules('user_pass_confirm', 'UserPasswordConfirm', 'trim|required|matches[user_pass]', $pass_error);
        }

        $this->form_validation->set_rules('display_name', 'DisplayName', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('user_email', 'UserEmail', 'trim|required|valid_email|min_length[6]|max_length[100]');
        $this->form_validation->set_rules('user_level', 'UserLevel', 'required|is_natural|less_than[4]');
        if(!$this->form_validation->run()){
            throw new Exception($this->form_validation->error_string());
        }
    }

    /**
     * 用户列表
     */
    public function index()
    {
        if (!empty($this->input->get())) {
            exit($this->user_model->get_user($this->input->get(), FALSE));
        }
        $this->load->view('');
    }

    /**
     * 新增用户
     */
    public function create()
    {
        $this->user_role_model->save_user_role(1, [1]);
        $this->load->helper('form');
        if (IS_AJAX && IS_POST) {
            try {
                $post = $this->input->post();
                if (empty($post)) {
                    throw new Exception('提交的内容不能为空');
                }
                $this->__formValidation();
                $this->user_model->save_user($post);
                send_json();
            } catch (Exception $e) {
                send_json(FALSE, $e->getMessage());
            }
        } else {
            $data['user_level'] = $this->user_level;
            $data['role_list'] = $this->role_model->get_role(['role_status' => 1], TRUE);
            $this->load->view('', $data);
        }
    }

    /**
     * 更新用户
     */
    public function update()
    {
        $this->load->helper('form');
        try {
            $user_id = $this->input->get_post('user_id');
            if (IS_AJAX && IS_POST) {
                $this->__formValidation('update');
                if (empty($this->input->post())) {
                    send_json(FALSE, '提交的数据不能为空');
                }
                $this->user_model->save_user($this->input->post());
                send_json();
            } else {
                $data['user_info'] = $this->user_model->get($user_id);
                $data['user_level'] = $this->user_level;
                $data['user_role'] = $this->user_role_model->get_user_role($user_id);
                $data['role_list'] = $this->role_model->get_role(['role_status' => 1]);
                $this->load->view('', $data);
            }
        } catch (Exception $e) {
            send_json(FALSE, $e->getMessage());
        }
    }
}