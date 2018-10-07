<?php

/**
 * 彩票
 *
 * User: kendo
 */
class Ticket extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sys/sort_model');
        $this->load->helper('url');
    }


    public function getTicket(){
        header('Content-Type:application/json');
        exit($this->sort_model->get_ticket([], false));
    }
    /**
     * 获取开奖时间
     */
    public function getTime()
    {
        date_default_timezone_set('Asia/Shanghai');
        $id = $this->input->get('ticket_id');
        $now = time();
        switch (true) {
            case true: //PC28,PC蛋蛋（北京28）每日179期,从北京时间09:05分起,每5分钟一期
                $beginTime = strtotime(date('Y-m-d') . ' 09:05');
                $endTime = $beginTime + (178 * 300);
                if ($now >= $beginTime) {
                    if ($now < $endTime) {
                        $no = ceil(($now - $beginTime) / 300); //获取下一个5分钟
                        if ($no == (($now - $beginTime) / 300)) {
                            $no = $no + 1;
                        }
                        $next = $beginTime + $no * 300;
                    } else {  //第二天
                        $next = $beginTime + 86400;
                    }
                } else {
                    $next = strtotime(date('Y-m-d') . ' 09:05');
                }
                $date[1] = [
                    'ticketName' => 'PC28',
                    'openDate' => date('Y-m-d H:i:s', $next),
                    'openTime' => $next
                ];
//                break;
            case true: //加拿大28 开奖规则：从北京时间凌晨00:03分起,每3分30秒一期，每日393期
                $beginTime = strtotime(date('Y-m-d') . ' 00:03');
                $endTime = $beginTime + (210 * 392);
                if ($now >= $beginTime) {
                    if ($now < $endTime) {
                        $no = ceil(($now - $beginTime) / 210); //获取下一个3分30秒
                        if ($no == (($now - $beginTime) / 210)) {
                            $no = $no + 1;
                        }
                        $next = $beginTime + $no * 210;
                    } else {  //第二天
                        $next = $beginTime + 86400;
                    }
                } else {
                    $next = strtotime(date('Y-m-d') . ' 00:03');
                }
                $date[2] = [
                    'ticketName' => '加拿大28',
                    'openDate' => date('Y-m-d H:i:s', $next),
                    'openTime' => $next
                ];
//                break;
            case true: //重庆时时彩 开奖规则：每日120期,上午10点至晚上22点每10分钟一期,晚上22点05分至凌晨1点55分每5分钟一期
                $beginTime = strtotime(date('Y-m-d') . ' 10:00');
                $endTime = strtotime(date('Y-m-d') . ' 01:55') + 86400;
                $nightTime = strtotime(date('Y-m-d') . ' 22:00');


                if ($now >= $beginTime && $now <= $nightTime) { //10分钟一趟
                    $no = ceil(($now - $beginTime) / 600);
                    if ($no == (($now - $beginTime) / 600)) {
                        $no = $no + 1;
                    }
                    $next = $beginTime + $no * 600;
                } elseif ($now > $nightTime && $now <= $endTime) {
                    $no = ceil(time() / 300);
                    $next = $no * 300;
                } else {
                    $next = $beginTime;
                }
                $date[3] = [
                    'ticketName' => '重庆时时彩',
                    'openDate' => date('Y-m-d H:i:s', $next),
                    'openTime' => $next
                ];
//                break;
            case true: //北京赛车 开奖规则：每日179期,从北京时间09:07分起,每5分钟一期。
                $beginTime = strtotime(date('Y-m-d') . ' 09:07');
                $endTime = $beginTime + (300 * 178);
                if ($now >= $beginTime) {
                    if ($now < $endTime) {
                        $no = ceil(($now - $beginTime) / 300);
                        if ($no == (($now - $beginTime) / 300)) {
                            $no = $no + 1;
                        }
                        $next = $beginTime + ($no * 300);
                    } else {  //第二天
                        $next = $beginTime + 86400;
                    }
                } else {
                    $next = $beginTime;
                }
                $date[4] = [
                    'ticketName' => '北京赛车',
                    'openDate' => date('Y-m-d H:i:s', $next),
                    'openTime' => $next
                ];
//                break;
            case true: //幸运飞艇 ，开奖规则，每日180期，13:09:00开始，5分钟一期
                $beginTime = strtotime(date('Y-m-d') . ' 13:09');
                $endTime = $beginTime + (300 * 179);
                if ($now >= $beginTime) {
                    if ($now < $endTime) {
                        $no = ceil(($now - $beginTime) / 300);
                        if ($no == (($now - $beginTime) / 300)) {
                            $no = $no + 1;
                        }
                        $next = $beginTime + $no * 300;
                    } else {  //第二天
                        $next = $beginTime + 86400;
                    }
                } else {
                    $next = $beginTime;
                }
                $date[5] = [
                    'ticketName' => '幸运飞艇',
                    'openDate' => date('Y-m-d H:i:s', $next),
                    'openTime' => $next
                ];
                break;
            case 6: //香港六合彩

                break;
        }
        header('Content-Type:application/json');
        exit(json_encode(['status' => TRUE, 'message' => 'Success', 'data' => $date]));
    }

    /**
     * 默认界面
     */
    public function index()
    {
        $params = $this->input->get();
        $data['title'] = '排序';
        if (IS_AJAX && IS_GET) {
            try {
                exit($this->sort_model->get_ticket($params, false));
            } catch (Exception $e) {
                send_json(FALSE, $e->getMessage());
            }
        } else {
            $this->load->helper('form');
            $data['sort_list'] = $this->sort_model->get_ticket($params);
            $this->load->view('sys/ticket/index', $data);
        }
    }

    /**
     * 更新排序
     */
    public function update()
    {
        if (IS_AJAX) {
            try {
                if (IS_GET) {
                    $this->load->helper('form');
                    $data['title'] = '更新排序';
                    $ticket_id = $this->input->get_post('ticket_id');
                    $data['sort'] = $this->sort_model->get($ticket_id);
                    send_json(TRUE, $this->load->view('', $data, TRUE));
                } else {
                    $this->sort_model->save_ticket($this->input->post());
                    exit(json_encode(['status' => TRUE, 'message' => 'Success']));
                }
            } catch (Exception $e) {
                send_json(FALSE, $e->getMessage());
            }
        }
    }
}