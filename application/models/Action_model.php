<?php
class Action_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }
    public function addaction ($data) {
        $result = $this->db->insert('gd_act', $data);
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '申请成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '申请失败', 'data' => ''));
        }
    }
}
