<?php
class Action extends CI_Controller {
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->database();
        $this->load->model('action_model');
        $this->load->helper('url_helper');
    }

    public function getActionList(){
        $this->db->limit(10);
        $this->db->select('gd_act.*,gd_user.user_id,gd_user.user_name,gd_user.user_image'); //查询的字段
        $this->db->from('gd_act'); //连表的主表
        $this->db->join('gd_user', ' gd_act.act_leader =  gd_user.user_id', 'left'); //连接表       
        $result =$this->db->get();
        $result = $result->result_array();
        if (count($result)) {
            echo json_encode(array('code' => 1, 'msg' => '为你查找到'.count($result).'条数据', 'data' => $result));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '查找失败', 'data' => $result));
        }
    }

    public function getActionDetail () {
        $act_id = $this->input->post("act_id");
        $this->db->select('gd_act.*,gd_user.user_id,gd_user.user_name,gd_user.user_image'); //查询的字段
        $this->db->where('act_id',$act_id);
        $this->db->from('gd_act'); //连表的主表
        $this->db->join('gd_user', ' gd_act.act_leader =  gd_user.user_id', 'left'); //连接表       
        $result =$this->db->get();
        $result = $result->row_array();
        $userimage = './src/data/' . $result['user_id'] . '/img/' . $result['user_image'];
                if (@fopen($userimage, 'r'))   //如果能打开照片
                {
                    $userimageType = explode(".", $result['user_image'])[1];
                    $userimagestr = file_get_contents($userimage); //把图片读取成字符串
                    $userimageBase64 = base64_encode($userimagestr);
                    $result['user_image'] = 'data:image/' . $userimageType . ';base64,' . $userimageBase64;
                } else {
                    $result['user_image'] = '';
                }
        if ($result) {
            echo json_encode(array('code' => 1, 'msg' => '查找成功', 'data' => $result));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '查找失败', 'data' => $result));
        }
    }

    public function addaction(){
        $act_name = $this->input->post("act_name");
        $act_date = $this->input->post("act_date");
        $act_leader = $this->input->post("act_leader");
        $act_detail = $this->input->post("act_detail");
        $act_member = $this->input->post("act_member");
        $act_send_date = $this->input->post("act_send_date");
        $act_type = $this->input->post("act_type");
        $act_id=md5($act_leader.$act_date.$act_name.$act_send_date);
        $data=array('act_id'=>$act_id,'act_name'=>$act_name,'act_date'=>$act_date,'act_leader'=>$act_leader,'act_detail'=>$act_detail,'act_member'=>$act_member,'act_type'=>$act_type,'act_send_date'=>$act_send_date);
        $result = $this->action_model->addaction($data);
        echo $result;
    }

    public function deleteAction () {
        $id = $this->input->post('act_id');
        $this->db->where('act_id', $id);
        $result = $this->db->delete('gd_act');
        if ($result) {
            echo json_encode(array('code' => 1, 'msg' => '删除成功', 'data' => ''));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '删除失败', 'data' => ''));
        }
    }
}
?>