<?php
header('Content-Type:application/json; charset=utf-8');
class Login_model extends CI_Model {
    public function __construct () {
        $this->load->database();  
    }

    public function getdata () {
        $query = $this->db->get('gd_test');
        return $query->result_array();
    }

    public function login ($id,$pwd,$last_login) {
        $result = $this->db->query("SELECT * FROM gd_user WHERE user_id = '$id' AND  user_password = '$pwd' ");
        $result = $result->row_array();
        if(!isset($result)){
            return json_encode(array('code'=>0,'msg'=>'login fail','data'=>$result));
            $_SESSION['vchenzhecom'] = null;
        } else {
            $_SESSION['vchenzhecom'] = $id;
            $this->db->set('last_login',$last_login, TRUE);
            $this->db->where('user_id', $id);
            $this->db->update('gd_user');
            return json_encode(array('code'=>1,'msg'=>'login success','data'=>$result));
        }
    }
}

?>