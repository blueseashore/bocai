<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Libraries
 * @author        EllisLab Dev Team
 * @link        https://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller
{

    /**
     * Reference to the CI singleton
     *
     * @var    object
     */
    private static $instance;

    public $need_login = TRUE;

    /**
     *
     * CI_Controller constructor.
     */
    public function __construct()
    {
        self::$instance =& $this;
        // Assign all the class objects that were instantiated by the
        // bootstrap file (CodeIgniter.php) to local class variables
        // so that CI can run as one big super object.
        foreach (is_loaded() as $var => $class) {
            $this->$var =& load_class($class);
        }
        $this->load =& load_class('Loader', 'core');
        $this->load->library('session');//开启session
        $this->load->initialize();
        $this->check_login();
        log_message('info', 'Controller Class Initialized');
    }

    // --------------------------------------------------------------------

    /**
     * Get the CI singleton
     *
     * @static
     * @return    object
     */
    public static function &get_instance()
    {
        return self::$instance;
    }

    /**
     * 登录检查/SESSION延时
     */
    private function check_login()
    {
        if ($this->need_login) {
            $user_data = $this->session->userdata();
            $method = $this->router->fetch_method();
            //登录页面登录判断
            if (strtolower($method) == 'login') {
                if (isset($user_data['user_name'])) {
                    if (!IS_AJAX) {
                        header('location:/');
                    }
                }
            } else {
                if (empty($user_data) || !isset($user_data['user_name'])) {
                    if (!IS_AJAX) {
                        header('location:/login');
                    } else {
                        $msg = 'Please Login Agian';
                        exit(json_encode(['status' => FALSE, 'message' => $msg]));
                    }
                } elseif ($user_data['expire_time'] <= time()) {   //SESSION超时
                    if (!IS_AJAX) {
                        header('location:/login');
                    } else {
                        $msg = 'Session Time Out';
                        exit(json_encode(['status' => FALSE, 'message' => $msg]));
                    }
                }
                $this->session->set_userdata('expire_time', time() + 3600);
            }
        }
    }
}
