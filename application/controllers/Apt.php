<?php
class Apt extends CI_Controller {
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->database();
        $this->load->model('apt_model');
        $this->load->helper('url_helper');
    }

    public function addApt(){
        $apt_time = $this->input->post("apt_time");
        $apt_fotor = $this->input->post("apt_fotor");
        $apt_applyer = $this->input->post("apt_applyer");
        $apt_date = $this->input->post("apt_date");
        $apt_detail = $this->input->post("apt_detail");
        $apt_qq = $this->input->post("apt_qq");
        $apt_wx = $this->input->post("apt_wx");
        $apt_kind = $this->input->post("apt_kind");
        $apt_type = 0;
        $apt_res = 0;
        $apt_id=md5($apt_time.$apt_fotor.$apt_applyer.$apt_date);
        $data = array(
            'apt_id' => $apt_id,
            'apt_time' => $apt_time,
            'apt_fotor' => $apt_fotor,
            'apt_applyer' => $apt_applyer,
            'apt_date' => $apt_date,
            'apt_detail' => $apt_detail,
            'apt_qq' => $apt_qq,
            'apt_wx' => $apt_wx,
            'apt_type' => $apt_type,
            'apt_res' => $apt_res,
            'apt_kind' => $apt_kind // 约拍种类
        );
        $result = $this->apt_model->addApt($data);
        echo $result;
    }

    public function getApt(){
        // 找我约拍的
        $data = $this->input->post("userid"); //通过摄影师id搜索
        $result = $this->apt_model->getApt($data);
        echo $result;
    }

    public function getMyApt(){
        // 我约拍的
        $data = $this->input->post("userid"); //通过摄影师id搜索
        $result = $this->apt_model->getMyApt($data);
        echo $result;
    }

    public function readApt(){
        // 摄影师阅读业务
        $apt_id = $this->input->post("apt_id");
        $this->db->set('apt_type', '1', FALSE);
        $this->db->where('apt_id', $apt_id);
        $this->db->update('gd_apt');
    }

    public function rejectApt(){
        // 摄影师拒绝接受业务
        $apt_id = $this->input->post("apt_id");
        $this->db->set('apt_res', '0', FALSE);
        $this->db->where('apt_id', $apt_id);
        $this->db->update('gd_apt');
    }

    public function deleteApt(){
        $apt_id = $this->input->post("apt_id");
        $this->db->where("apt_id",$apt_id);
        $result = $this->db->delete("gd_apt");
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '申请成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '申请失败', 'data' => ''));
        } 
    }
}
?>