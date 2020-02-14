<?php
class Login extends CI_Controller {
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->model('login_model');
        $this->load->helper('url_helper');
    }
    public function tolog () {
        $id = $this->input->post("id");
        $pwd = $this->input->post("pwd");
        $last_login = $this->input->post("last_login");
        $loginResult = $this->login_model->login($id,$pwd,$last_login);
        echo $loginResult;
    }

    public function logout () {
        $_SESSION['vchenzhecom'] = null;
        echo json_encode(array('code'=>0,'msg'=>'quit successfully'));
    }

    public function userdata () {
        $data = $this->login_model->getdata();
        echo json_encode($data);
    }

    public function islogin () {
        if(!isset($_SESSION['vchenzhecom']) || empty($_SESSION['vchenzhecom']) ) {
            echo json_encode(array('code'=>0,'msg'=>'not login'));
        } else {
            echo json_encode(array('code'=>0,'msg'=>'already logined','data' => $_SESSION['vchenzhecom']));
        }
    }
}

?>