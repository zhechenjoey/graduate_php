<?php
class MY_Controller extends CI_Controller {
	public function __constructor () {
        if(!isset($_SESSION)){
            session_start();
        }
    }
    public function checkLogin () {
        if(!isset($_SESSION['vchenzhecom']) || empty($_SESSION['vchenzhecom']) ) {
            header('location:Pagerror'); // 跳转到错误页
        } 
    }

    public function uploadVideo ($id) {
        if (!empty($_FILES['tv_file']['tmp_name'])) {
            $fileType = substr($_FILES["tv_file"]["type"], 6); //获取image/后面的文件类型
            $fileName = md5($id. date("Y-m-d")) . '.' . $fileType;
            //===============================================
            move_uploaded_file(
                $_FILES["tv_file"]["tmp_name"], //重要！！！！
                './src/data/' . $id . '/video/' . $fileName
            );
            $image = $fileName;
        } 
    }
}
?>