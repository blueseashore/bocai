<?php

/**
 * 默认首页
 *
 * User: kendo
 */
class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    public function index($page = 'index')
    {
        if (!file_exists(APPPATH . 'views/home/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = '网站后台'; // Capitalize the first letter
        $this->load->view('layout/header', $data);
        $this->load->view('layout/index', $data);
        $this->load->view('layout/footer', $data);
    }
}