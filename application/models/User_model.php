<?php
class User_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }
    public function getuserinfo($id)
    {
        $result = $this->db->query("SELECT * FROM gd_user WHERE user_id = '$id' ");
        $result = $result->row();
        $imgPic = './src/data/' . $result->user_id . '/img/' . $result->user_image; //获取图片地址
        if (@fopen($imgPic, 'r'))   //如果能打开照片
        {
            $imgType = explode(".", $result->user_image)[1]; //获取头像的类型
            $imgstr = file_get_contents($imgPic); //把图片读取成字符串
            $imgBase64 = base64_encode($imgstr);
            $result->user_image = 'data:image/' . $imgType . ';base64,' . $imgBase64;
        }
        return json_encode(array('code' => 1, 'msg' => 'ok', 'data' => $result));
    }
    public function updateuserinfo($id, $name, $fileName, $gender, $birth, $tag, $explain)
    {
        $data = array(
            'user_id' => $id,
            'user_name' => $name,
            'user_image' => $fileName,
            'user_gender' => $gender,
            'user_birth' => $birth,
            'user_tag' => $tag,
            'user_explain' => $explain
        );
        $this->db->where('user_id', $id);

        $result = $this->db->update('gd_user', $data);
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => 'update ok', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => 'update fail', 'data' => ''));
        }
    }
}
