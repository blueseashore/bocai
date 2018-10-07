<?php

/**
 * 彩票模型
 *
 * User: kendo
 */
class Sort_model extends CI_Model
{
    private $_table = 'sys_ticket';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 获取单个数据
     *
     * @param int $ticket_id
     * @return array
     * @throws Exception
     */
    public function get($ticket_id = 0)
    {
        $role = $this->db->get_where($this->_table, ['ticket_id' => $ticket_id])->row_array();
        if (empty($role)) throw new Exception('请传入正确的ID');
        return $role;
    }

    /**
     * 获取列表
     *
     * @param array $param
     * @param bool $is_array
     * @param bool $is_page
     * @return array|string
     */
    public function get_ticket(array $param, $is_array = TRUE, $is_page = TRUE)
    {
        $sort_list = $this->db->order_by('sort_num', 'asc')->get_where($this->_table, [])->result_array();
        if ($is_array) {
            return $sort_list;
        } else {
            $result = $this->db->simple_query(filter_limit_sql($this->db->last_query()));
            return send_list_json($sort_list, $result->num_rows);
        }
    }

    /**
     * 保存彩票设置
     *
     * @throws Exception
     */
    public function save_ticket()
    {
        if (!empty($this->input->post('ticket_id'))) {
            $this->db->reset_query();
            $this->db->set('sort_num', $this->input->post('sort_num'));
            $this->db->where('ticket_id', $this->input->post('ticket_id'));
            $this->db->update($this->_table);
        } else {
            throw new Exception('非法提交');
        }
    }
}