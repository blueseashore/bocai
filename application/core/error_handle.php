<?php
/**
 * 重写错误展示
 * User: kendo
 */

/**
 * 重写404页面，AJAX提交时，返回JSON数据
 */
if (!function_exists('show_404')) {
    function show_404($page = '', $log_error = TRUE)
    {
        if (IS_AJAX) {
            exit(json_encode(['status' => FALSE, 'message' => 'The page you requested was not found.']));
        }
        $_error =& load_class('Exceptions', 'core');
        $_error->show_404($page, $log_error);
        exit(4); // EXIT_UNKNOWN_FILE
    }
}

//发送运行错误的时候
if (!function_exists('_error_handler')) {
    function _error_handler($severity, $message, $filepath, $line)
    {
        if (ENVIRONMENT == 'production') {

        } else {    //非正式环境
            if (IS_AJAX) {
                exit(json_encode(['status' => FALSE, 'message' => 'File:' . $filepath . '<br/>Line:' . $line . '<br/>Message:' . $message]));
            } else {
                $is_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

                if ($is_error) {
                    set_status_header(500);
                }

                if (($severity & error_reporting()) !== $severity) {
                    return;
                }

                $_error =& load_class('Exceptions', 'core');
                $_error->log_exception($severity, $message, $filepath, $line);

                if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'))) {
                    $_error->show_php_error($severity, $message, $filepath, $line);
                }

                if ($is_error) {
                    exit(1); // EXIT_ERROR
                }
            }
        }
    }
}

//发生未捕获的异常的时候
if (!function_exists('_exception_handler')) {
    function _exception_handler($exception)
    {
        if (ENVIRONMENT == 'production') {
            $message = '程序发生未被捕获的异常';
            if (IS_AJAX) {
                exit(json_encode(['status' => FALSE, 'message' => $message]));
            } else {
                exit($message);
            }
        } else {
            if (IS_AJAX) {
                exit(json_encode(['status' => FALSE, 'message' => 'File:' . $exception->getFile() . '<br/>Line:' . $exception->getLine() . '<br/>Message:' . $exception->getMessage()]));
            } else {
                $_error =& load_class('Exceptions', 'core');
                $_error->log_exception('error', 'Exception: ' . $exception->getMessage(), $exception->getFile(), $exception->getLine());
                is_cli() OR set_status_header(500);
                // Should we display the error?
                if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'))) {
                    $_error->show_exception($exception);
                }
                exit(1); // EXIT_ERROR
            }
        }
    }
}