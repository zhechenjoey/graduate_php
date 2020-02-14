<?php
class Friends extends CI_Controller {
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->database();
        //$this->load->model('action_model');
        $this->load->helper('url_helper');
    }

    public function getFollow () {
        $follow_id = $this->input->post('follow_id');
        $this->db->select('gd_follow.followed_id,gd_user.user_name,gd_user.user_image,gd_user.user_follow,gd_user.user_fans'); //查询的字段
        $this->db->from('gd_follow'); //连表的主表
        $this->db->where("gd_follow.follow_id = '$follow_id'");
        $this->db->join('gd_user', ' gd_follow.followed_id =  gd_user.user_id', 'left'); //连接表
        $result = $this->db->get()->result_array();
        if($result){
            echo json_encode(array('code' => 1, 'msg' => '为您找到'.count($result).'条用户', 'data' => $result));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '未点赞', 'data' => ''));
        }
    }
}
?>