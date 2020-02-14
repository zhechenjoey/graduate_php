<?php
class Apt_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }
    public function addApt ($data) {
        $result = $this->db->insert('gd_apt', $data);
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '申请成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '申请失败', 'data' => ''));
        } 
    }

    public function getApt($data){
        // 查询找我拍照的
        $this->db->select('gd_apt.*,gd_user.user_id,gd_user.user_name,gd_user.user_image'); //查询的字段    
        $this->db->from('gd_apt'); //连表的主表
        $this->db->where("gd_apt.apt_fotor = '$data' "); //查询的条件
        $this->db->join('gd_user', ' gd_apt.apt_applyer =  gd_user.user_id', 'left'); //连接表
        $this->db->order_by('apt_time', 'DESC');
        $result = $this->db->get()->result_array(); //语句查询，切记->result_array()；必须的有
        if (count($result)>0) {
            return json_encode(array('code' => 1, 'msg' => 'search success', 'data' => $result));
        } else {
            return json_encode(array('code' => 0, 'msg' => 'search fail', 'data' => ''));
        } 
    }

    public function getMyApt($data){
        // 查询我的申请
        $this->db->select('gd_apt.*,gd_user.user_id,gd_user.user_name,gd_user.user_image'); //查询的字
        $this->db->from('gd_apt'); //连表的主表
        $this->db->where("gd_apt.apt_applyer = '$data' "); //查询的条件
        $this->db->join('gd_user', ' gd_apt.apt_fotor =  gd_user.user_id', 'left'); //连接表
        $this->db->order_by('apt_time', 'DESC');
        $result = $this->db->get()->result_array(); //语句查询，切记->result_array()；必须的有
        if (count($result)>0) {
            return json_encode(array('code' => 1, 'msg' => 'search success', 'data' => $result));
        } else {
            return json_encode(array('code' => 0, 'msg' => 'search fail', 'data' => ''));
        } 
    }
}
