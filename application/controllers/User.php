<?php
header("Content-Type:Application/json;charset=utf8");
defined('BASEPATH') or exit('No direct script access allowed');
include 'CompressPicture.php';
class User extends MY_Controller
{
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->model('user_model');
        $this->load->helper('url_helper');
        $this->load->helper(array('form', 'url'));
    }
    public function index()
    {
    }
    public function getinfo()
    {
        $id = $this->input->post("id");
        $result = $this->user_model->getuserinfo($id);
        echo $result;
    }

    public function updateinfo()
    {
        $id = $this->input->post("user_id");
        $name = $this->input->post("user_name");
        $gender = $this->input->post("user_gender");
        $birth = $this->input->post("user_birth");
        $explain = $this->input->post("user_explain");
        $tag = $this->input->post("user_tag");
        $searchImage = $this->db->query("SELECT user_image FROM gd_user WHERE user_id = '$id' ");
        $searchImage = $searchImage->row();
        if (!empty($_FILES['user_image']['tmp_name'])) {
            $fileType = substr($_FILES["user_image"]["type"], 6); //获取image/后面的文件类型
            $fileName = $id . '.' . $fileType;
            $savepath = './src/data/' . $id . '/img/';
            //===============================================
            // move_uploaded_file(
            //     $_FILES["user_image"]["tmp_name"], //重要！！！！
            //     './src/data/' . $id . '/img/' . $fileName
            // );
            move_uploaded_file($_FILES['user_image']['tmp_name'], $savepath . $fileName);
            $source =  $savepath . $fileName; //原图文件名
            $dst_img = $fileName; //保存图片的文件名
            $percent = 0.1;  #原图压缩，不缩放，但体积大大降低
            $image = (new imgcompress($source, $percent))->compressImg($dst_img, $savepath);
            $image = $fileName;
        } else {
            $image = $searchImage->user_image;
        }
        $result = $this->user_model->updateuserinfo($id, $name, $image, $gender, $birth, $tag, $explain);
        echo $result;
    }
}
