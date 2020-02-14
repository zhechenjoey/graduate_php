<?php
header('Content-Type:application/json; charset=utf-8');
class Register_model extends CI_Model {
    public function __construct () {
        $this->load->database();  
    }

    public function checkId ($id) {
        $result = $this->db->query("SELECT * FROM gd_user WHERE user_id = $id ");
        $result = $result->row_array();
        if ($result == null) { //此账号可以使用
            return 1;
        } else {
            return 0;
        }
    }

    public function checkMail ($mail) {
        $result = $this->db->query("SELECT * FROM gd_user WHERE user_mail = $mail ");
        $result = $result->row_array();
        if ($result == null) { //此邮箱可以使用
            return 1;
        } else {
            return 0;
        }
    }

    public function addUserToDB ($id,$password,$mail,$role){
        $checkId = $this->checkId($id);
        if($checkId){
            $path="src/img/".$id; //判断目录存在否，存在给出提示，不存在则创建目录
            if (is_dir($path)){
                $result = ['code'=>'1','msg'=>'覆盖用户目录']; 
            }
            else{//第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
                $res=mkdir(iconv("UTF-8", "GBK", $path),0777,true); 
            }
            $data = array('user_id' => $id, 'user_password' => $password,'user_mail' => $mail, 'user_role'=>$role);
            $this->db->insert('gd_user', $data);
            return 1;
        } else {
            return 0;
        }
    }
}
?>