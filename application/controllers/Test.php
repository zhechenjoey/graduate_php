<?php
class Test extends CI_Controller {
	public function index () {
		$id = $this->input->post("id");
        $pwd = $this->input->post("pwd");
        echo $id;
	}
}
?>