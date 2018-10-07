<?php
/**
 * User: kendo    2018/2/2
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

Class Excel
{
    private $_excelSheet;

    /**
     * 检测EXCEL表头是否合法
     *
     * A-Z  65-90
     * @param array $header_column
     * @throws Exception
     */
    public function check_excel(array $header_column)
    {
        foreach ($header_column as $key => $column) {
            if ($this->_excelSheet->getCell(strtoupper(chr(65 + $key)) . '1')->getValue() !== $column) {
                throw new Exception(strtoupper(chr(65 + $key)) . '1，名称不是:' . $column);
            }
        }
    }

    /**
     * 获取EXCEL数据
     *
     * @param string $file_name EXCEL文件位置
     * @param array $header_column EXCEL工作表表头
     * @param int $active_sheet EXCEL当前工作表
     * @return PHPExcel_Worksheet   返回当前工作表数据
     * @throws Exception
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function get_data($file_name = '', $header_column = [], $active_sheet = 0)
    {
        $excelReader = PHPExcel_IOFactory::createReader('Excel2007');
        $excelList = $excelReader->load($file_name);
        $excelList->setActiveSheetIndex($active_sheet);
        $excelSheet = $excelList->getActiveSheet();
        $this->_excelSheet = $excelSheet;
        if (!empty($header_column)) {
            $this->check_excel($header_column);
        }
        if ($excelSheet->getHighestDataRow() < 1 + intval(!empty($header_column))) {
            throw new Exception('上传的EXCEL没有有效的行数据');
        }
        if ($excelSheet->getHighestDataRow() > 1000) {
            throw new Exception('上传的EXCEL行数大于1000行');
        }
        if ($excelSheet->getHighestColumn() > 26) {
            throw new Exception('上传的EXCEL列数不可超过26列');
        }
        return $this->_excelSheet;
    }

    /**
     * 导出数据选择器
     *
     * @param array $data
     * @param string $type
     * @throws Exception
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    public function export_data(array $data, $type = 'excel')
    {
        if ($type === 'excel') {
            $this->export_excel($data);
        } elseif ($type === 'csv') {
            $this->export_csv($data);
        }
    }

    /**
     * 导出数据，格式：EXCEL
     *
     * @param array $data
     * @param string $type
     * @return string
     * @throws Exception
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    public function export_excel(array $data, $type = 'php://output')
    {
        error_reporting(0);
        if (empty($data['list'])) {
            throw new Exception('EXCEL导出内容为空，不处理');
        }
        if (!empty($data['table_header'])) {
            if (count($data['table_header']) > count($data['list'][0])) {
                throw new Exception('数据列数少于EXCEL所需列数');
            }
        }
        $phpExcel = new PHPExcel();
        if ($type === 'php://output') {     //设置字节流头部
            ob_end_clean();
            header("Content-type:text/html;charset=utf-8");
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition:attachment;filename=" . $data['table_name'] . ".xlsx");
            header('Cache-Control: max-age=0');
        }
        //设置EXCEL基础信息
        $phpExcel->getProperties()->setCreator("kendo")
            ->setLastModifiedBy("kendo")
            ->setTitle($data['table_name'])
            ->setSubject($data['table_name'])
            ->setDescription($data['table_name'])
            ->setKeywords("excel")
            ->setCategory("result file");

        $phpExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $phpExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet = $phpExcel->setActiveSheetIndex(0);

        $head_start = 1;
        if (isset($data['title'])) {
            $sheet->mergeCells('A1:' . chr(ord('A') + count($data['table_header']) - 1) . '1');
            $sheet->setCellValue('A1', $data['title']);
            $head_start = 2;
        }
        $tmpNum = 0;
        foreach ($data['table_header'] as $v) {
            $sheet->setCellValue($this->check_key($tmpNum) . $head_start, $v);
            $cellBorderObj = $sheet->getStyle($this->check_key($tmpNum) . $head_start)->getBorders();
            $this->set_border($cellBorderObj);
            $sheet->getStyle($this->check_key($tmpNum) . $head_start)->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DDDDDD');
            $phpExcel->getActiveSheet()->getRowDimension($head_start)->setRowHeight(20);
            $tmpNum++;
        }
        $num = isset($data['title']) ? 3 : 2;
        foreach ($data['list'] as $value) {
            $cellNum = 0;
            foreach ($value as $cell) {
                if (strpos($cell, '=') === 0) {
                    $cell = '\'' . $cell;
                }
                $sheet->setCellValueExplicit($this->check_key($cellNum) . $num, $cell, PHPExcel_Cell_DataType::TYPE_STRING);
                $cellBorderObj = $sheet->getStyle($this->check_key($cellNum) . $num)->getBorders();
                $this->set_border($cellBorderObj);
                $cellNum++;
            }
            $num++;
        }

        $phpExcel->getActiveSheet()->setTitle("data");
        for ($i = 0; $i <= count($data['table_header']); $i++) {
            $phpExcel->getActiveSheet()->getColumnDimension($this->check_key($i))->setAutoSize(true);
        }

        $phpExcel->setActiveSheetIndex(0);
        $excelWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        if ($type === 'php://output') {
            $excelWriter->save($type);
            exit;
        } else {
            $final_path = FCPATH . 'assets/download/' . date('Ym');
            if (!file_exists($final_path)) {
                @mkdir($final_path, 0755, TRUE);
            }
            $final_name = $final_path . '/' . $data['table_name'] . '.xlsx';
            $excelWriter->save($final_name);
            return $final_name;
        }
    }

    /**
     * 导出数据，格式：CSV字节流
     *
     * @param array $data
     */
    public function export_csv(array $data)
    {
        error_reporting(0);
        ob_end_clean();
        header("Content-type:text/csv;charset=utf-8");
        header("Content-Disposition:attachment;filename=" . $data['table_name'] . '.csv');
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');

        $csvStr = chr(0xEF) . chr(0xBB) . chr(0xBF);
        foreach ($data['table_header'] as $k => $v) {
            $tmp = str_replace(array("\n", "\r", ','), array('', '', ' & '), $v);
            $csvStr .= ($k == 0) ? $tmp : ',' . $tmp;
        }
        $csvStr .= "\n";

        foreach ($data['list'] as $value) {
            foreach ($value as $key => $cell) {
                $tmp1 = str_replace(array("\n", "\r", ','), array('', '', ' & '), $cell);
                $csvStr .= ($key == 0) ? $tmp1 : ',' . $tmp1;
            }
            $csvStr .= "\n";
        }
        exit($csvStr);
    }

    /**
     * 导出数据，格式：CSV文件
     *
     * @param array $data
     * @return bool|int 失败时返回FALSE，成功时返回写入的字节数
     */
    public function export_csv_file(array $data)
    {
        $csv_str = '';
        $i = 0;
        foreach ($data['table_header'] as $k => $v) {
            $v = str_replace(array("\n", "\r", ','), array('', '', ' & '), $v);
            $csv_str .= ($i == 0) ? $v : ',' . $v;
            $i++;
        }
        $csv_str .= "\n";
        foreach ($data['list'] as $value) {
            $i = 0;
            foreach ($value as $key => $cell) {
                $tmp1 = str_replace(array("\n", "\r", ','), array('', '', ' & '), $cell);
                $csv_str .= ($i == 0) ? $tmp1 : ',' . $tmp1;
                $i++;
            }
            $csv_str .= "\n";
        }
        $file_path = FCPATH . 'assets/download/' . date('Ym');
        if (!file_exists($file_path)) {
            @mkdir($file_path, 0755, TRUE);
        }
        $final_name = $file_path . '/' . $data['table_name'] . '.csv';
        return file_put_contents($final_name, $csv_str);
    }

    /**
     * @param $num
     * @return string
     * @throws Exception
     */
    public function check_key($num)
    {
        $keyNumStr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $first = (int)(($num + 1) / 26);
        $second = ($num + 1) % 26;
        if ($first < 26) {
            if ($first == 0 || ($first == 1 && $second == 0)) {
                return $keyNumStr[$num];
            } elseif ($second == 0) {
                return $keyNumStr[$first - 2] . $keyNumStr[25];
            } else {
                return $keyNumStr[$first - 1] . $keyNumStr[$second - 1];
            }
        } else {
            throw new Exception('请查看数据是否正确');
        }
    }

    /**
     * 设置边框
     *
     * @param PHPExcel_Style_Borders $cellBoderObj
     */
    public function set_border($cellBoderObj)
    {
        $cellBoderObj->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $cellBoderObj->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $cellBoderObj->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $cellBoderObj->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    }
}