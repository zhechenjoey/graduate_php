<?php

class Login extends CI_Controller {
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->model('login_model');
        $this->load->helper('url_helper');
    }
    public function index () {

    }

    public function logout () {
        $_SESSION['vchenzhecom'] = null;
    }

    public function userData () {
        $data = $this->login_model->getdata();
        echo json_encode($data);
    }
}

?>