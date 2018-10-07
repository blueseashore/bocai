<?php
/**
 * User: kendo
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 图片的配置
 */
$config['upload']['image']['upload_path'] = BASEUPLOAD.'/image/';
$config['upload']['image']['allowed_types'] = 'gif|jpg|jpeg|png';
$config['upload']['image']['max_size'] = '2048';
$config['upload']['image']['max_width'] = '1024';
$config['upload']['image']['max_height'] = '768';

/**
 * EXCEL的配置
 */
$config['upload']['excel']['upload_path'] = BASEUPLOAD.'/excel/';
$config['upload']['excel']['allowed_types'] = 'xls|xlsx';
$config['upload']['excel']['max_size'] = '1024';


/**
 * CSV的配置
 */
$config['upload']['csv']['upload_path'] = BASEUPLOAD.'/csv/';
$config['upload']['csv']['allowed_types'] = 'csv';
$config['upload']['csv']['max_size'] = '1024';