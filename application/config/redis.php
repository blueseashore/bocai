<?php
/**
 * User: kendo
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/*
$config = array(
    'default' => array(
        'socket_type' => 'tcp',
        'socket' => '/var/run/redis.sock',
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'timeout' => 0,
    ),
);
*/

//$config['socket_type'] = 'tcp'; //`tcp` or `unix`
//$config['socket'] = '/var/run/redis.sock'; // in case of `unix` socket type
$config['host'] = '127.0.0.1';
$config['password'] = NULL;
$config['base_url'] = NULL;
$config['port'] = 6379;
$config['timeout'] = 0;