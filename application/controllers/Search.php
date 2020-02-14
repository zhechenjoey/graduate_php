<?php
class Search extends CI_Controller {
    public function __construct () {
        session_start();
        parent::__construct();
        $this->load->model('search_model'); 
        $this->load->helper('url_helper');
    }

    public function searchUser () {
        $id=$this->input->post("id");
        $result = $this->search_model->searchUser($id);
        echo $result;
    }

    public function searchVideo () {
        $name = $this->input->post("name");
        $result = $this->search_model->searchVideo($name);
        echo $result;
    }
}
?>