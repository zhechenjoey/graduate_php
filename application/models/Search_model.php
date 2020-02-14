<?php
class Search_model extends CI_Model {
    public function __construct()
    {
        $this->load->database();
    }
    public function searchUser($id){
        $result = $this->db->query("SELECT * FROM gd_user WHERE user_id = $id or user_name = $id ");
        $result = $result->row_array();
        if($result == null ) {
            return json_encode(array('code'=>1,'msg'=>'没有搜索到用户','data'=>$result));
        } else {
            return json_encode(array('code'=>1,'msg'=>'为您找到这些用户','data'=>$result));
        }
    }
    public function searchVideo($name){
        $result = $this->db->query("SELECT * FROM gd_tv WHERE tv_name = $name ");
        $result = $result->row_array();
        if($result == null ) {
            return json_encode(array('code'=>1,'msg'=>'没有搜索到视频','data'=>$result));
        } else {
            return json_encode(array('code'=>1,'msg'=>'为您找到这些视频','data'=>$result));
        }
    }
}

?>