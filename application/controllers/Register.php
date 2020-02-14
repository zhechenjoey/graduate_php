<?php
class Register extends CI_Controller {
    public function __construct () {
        session_start();
        parent::__construct();
        $this->load->model('register_model'); 
        $this->load->helper('url_helper');
    }

    public function index () {
        $this->load->model('register_model'); 
        echo 1;
    }
    
    public function idExist () { //检查账号是否已经存在
        $id = $this->input->get('id');
        $idResult = $this->register_model->checkId($id);
        if($idResult == 0){ //账号已经存在，无法注册
            exit(json_encode(array('code'=>0,'msg'=>'id unavaliable,please try another one')));
        } else {
            exit(json_encode(array('code'=>1,'msg'=>'id avaliable')));
        }
    }

    public function mailExist () { //检查账号是否已经存在
        $mail = $this->input->get('mail');
        $this->load->model('register_model');
        $mailResult = $this->register_model->checkMail($mail);
        if($mailResult == 0){ //账号已经存在，无法注册
            exit(json_encode(array('code'=>0,'msg'=>'mail unavaliable,please try another one')));
        } else {
            exit(json_encode(array('code'=>1,'msg'=>'mail avaliable')));
        }
    }

    public function sendIdCode () { //发送验证码
        $mail = $this->input->get('mail');
        $_SESSION['vchenzhecomregisteridcode'] = '';
        echo json_encode(array('code'=>1,'msg'=>'验证码已发送'));
    }

    public function testIdCode () { //检测验证码
        $idCode = $this->input->get('idCode');
        if(isset($_SESSION['vchenzhecomregisteridcode']) && !empty($_SESSION['vchenzhecomregisteridcode']) ) {
            if($idCode == $_SESSION['vchenzhecomregisteridcode']) {
                echo json_encode(array('code'=>1,'msg'=>'the id code is correct'));
            }
        } else {
            echo json_encode(array('code'=>0,'msg'=>'the id code is wrong'));
        }
    }

    public function adduser () { //注册用户
        $id = $this->input->post('user_id');
        $password = md5($this->input->post('user_password'));
        //$name = $this->input->get('user_name');
        $mail = $this->input->post('user_mail');
        //$image = $this->input->get('user_image');
        //$gender = $this->input->get('user_gender');
        //$birth = $this->input->get('user_birth');
        //$explain = $this->input->get('user_explain');
        //$city = $this->input->get('user_city');
        //$role = $this->input->get('user_role');
        //$tag = $this->input->get('user_tag');
        $role = 1;
        //$addResult = $this->register_model->addUserToDB($id,$password,$name,$mail,$image,$gender,$birth,$explain,$city,$role,$tag);
        $addResult = $this->register_model->addUserToDB($id,$password,$mail,$role);
        if($addResult){
            echo json_encode(array('code'=>1,'msg'=>'register success'));
        } else {
            echo json_encode(array('code'=>0,'msg'=>'register fail'));
        }
    }
}

?>