<?php
class Home extends MY_Controller {
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->database();
        $this->load->helper('url_helper');
    }

    public function checkin () {
        // 签到
        $id = $this->input->post("userid");
        $last_check = $this->input->post("last_check");
        $this->db->set('last_check',$last_check, TRUE);
        $this->db->set('user_money','user_money+1', FALSE);
        $this->db->where('user_id', $id);
        $this->db->update('gd_user');
        echo json_encode(array('code' => 1, 'msg' => '搜索成功', 'data' => ''));
    }
}

?>