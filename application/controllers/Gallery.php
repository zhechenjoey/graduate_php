<?php
class Gallery extends CI_Controller
{
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->model('gallery_model');
        $this->load->helper('url_helper');
    }

    public function getAllPic()
    {
        $key = $this->input->post("searchkey"); //根据关键词，查找tag相近的图片
        $page = $this->input->post("page"); //根据关键词，查找tag相近的图片
        $limit = $this->input->post("limit"); //根据关键词，查找tag相近的图片
        $result = $this->gallery_model->searchPic($key,$page,$limit);
        echo $result;
    }

    public function searchPic(){
        $key = $this->input->post("searchkey"); //根据关键词，查找tag相近的图片
        $page = $this->input->post("page"); //根据关键词，查找tag相近的图片
        $limit = $this->input->post("limit"); //根据关键词，查找tag相近的图片
        $result = $this->gallery_model->searchPic($key,$page,$limit);
        echo $result;
    }

    public function getPic()
    {
        $id = $this->input->post('pic_id');
        $user_id = $this->input->post('pic_user_id');
        $imagepath = './src/data/' . $user_id . '/img/' . $id;
        if (@fopen($imagepath, 'r'))   //如果能打开照片
        {
            $userimageType = explode(".", $id)[1];
            $userimagestr = file_get_contents($imagepath); //把图片读取成字符串
            $userimageBase64 = base64_encode($userimagestr);
            $imagefile = 'data:image/' . $userimageType . ';base64,' . $userimageBase64;
            echo json_encode(array('code' => 1, 'msg' => '搜索成功', 'data' => $imagefile));
        } else {
            $this->db->where("pic_id",$id);
            $this->db->delete("gd_pic");
            echo json_encode(array('code' => 0, 'msg' => '打开图片失败，已删除此图片', 'data' => ''));
        }
    }
}
