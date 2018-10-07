<?php

/**
 * 前端控制器，不用走登录验证逻辑
 *
 * User: kendo
 */
class Front extends CI_Controller
{
    public $need_login = FALSE;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    /**
     * 登入
     */
    public function login()
    {
        $user_data = $this->session->userdata();
        if (!empty($user_data['user_name']) && $user_data['expire_time'] >= time()) {
            $this->load->helper('url');
            redirect('/');
        }
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('loginname', 'LoginName', 'required');
        $this->form_validation->set_rules('loginpass', 'LoginPassword', 'required');
//        $this->form_validation->set_rules('captchacode', 'CaptchaCode', 'required');
        if (IS_AJAX) {
            if ($this->form_validation->run() === FALSE) {
                $this->load->library('error', 'language');
                send_json(FALSE, $this->form_validation->error_string());
            } else {
                $this->load->model('sys/user_model');
                $this->user_model->login();
            }
        } else {
            $data['title'] = '登录页面';
            $this->load->view('sys/login', $data);
        }
    }

    /**
     * 登出
     */
    public function logout()
    {
        logout();
    }

    /**
     * 登录检测,返回JSON
     */
    public function is_login()
    {
        $user_data = $this->session->userdata();
        if (isset($user_data['user_name']) && isset($user_data['expire_time']) && $user_data['expire_time'] >= time()) {
            send_json();
        } else {
            send_json(FALSE);
        }
    }

    /**
     * 显示验证码
     *
     * 获取五次验证码
     */
    public function captcha()
    {
        $captcha = $this->session->userdata('captcha');
        $too_much = FALSE;
        if (is_null($captcha)) {
            $this->session->set_userdata('captcha', array('captcha_num' => 0));
        } elseif (isset($captcha['captcha_num']) && $captcha['captcha_num'] >= 5) {
            if (isset($captcha['captcha_expire_time']) && time() - $captcha['captcha_expire_time'] > 10) {
                $this->session->set_userdata('captcha', array('captcha_num' => 0));
            } else {
                $too_much = TRUE;
            }
        }
        $this->load->helper('captcha');
        $vals = array(
            'img_path' => APPPATH . '/captcha/',
            'img_url' => $this->config->item('base_url') . '/captcha/',
            'font_path' => './path/to/fonts/texb.ttf',
            'img_width' => 100,
            'img_height' => 38,
            'expiration' => 7200,
            'word_length' => 5,
            'font_size' => 50,
            'img_id' => 'Imageid',
            'pool' => '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ',
            'colors' => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(255, 40, 40)
            )
        );
        if ($too_much) {
            $vals['word'] = 'Too busy';
        }
        $cap = create_captcha($vals);
        if (!$too_much) {
            $captcha = $this->session->userdata('captcha');
            $captcha = array(
                'captcha_num' => ++$captcha['captcha_num'],
                'captcha_expire_time' => time(),
                'captcha_code' => $cap['word'],
            );
            $this->session->set_userdata('captcha', $captcha);
        }
    }
}