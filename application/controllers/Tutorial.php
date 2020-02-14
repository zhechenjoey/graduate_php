<?php
header("Content-Type:Application/json;charset=utf8");
defined('BASEPATH') or exit('No direct script access allowed');
include 'CompressPicture.php';
class Tutorial extends CI_Controller
{
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->database();
        $this->load->model('tutorial_model');
        $this->load->helper('url_helper');
    }

    // 搜索某个视频（添加到视频列表）
    public function getVideoList()
    {
        $search = $this->input->post("searchkey");
        $page = $this->input->post("page");
        $limit = $this->input->post("limit");
        $result = $this->tutorial_model->getVideoList($search,$page,$limit);
        echo $result;
    }

    // public function getVideo()
    // {
    //     $id = $this->input->post('tv_id');
    //     // $imagepath = './src/system/tv_image/'. $id;
    //     // if (@fopen($imagepath, 'r'))   //如果能打开照片
    //     // {
    //     //     $userimageType = explode(".", $id)[1];
    //     //     $userimagestr = file_get_contents($imagepath); //把图片读取成字符串
    //     //     $userimageBase64 = base64_encode($userimagestr);
    //     //     $imagefile = 'data:image/' . $userimageType . ';base64,' . $userimageBase64;
    //     //     echo json_encode(array('code' => 1, 'msg' => '搜索成功', 'data' => $imagefile));
    //     // } else {
    //         $imagepaths = './src/system/sys_image/video.jpg';
    //         $userimageType = 'jpg';
    //         $userimagestr = file_get_contents($imagepaths); //把图片读取成字符串
    //         $userimageBase64 = base64_encode($userimagestr);
    //         $imagefile = 'data:image/' . $userimageType . ';base64,' . $userimageBase64;
    //         echo json_encode(array('code' => 1, 'msg' => '搜索成功', 'data' => $imagefile));
    //     //}
    // }
    public function upload()
    {
        $name = $this->input->post('tv_name');
        $author = $this->input->post('tv_author');
        $tag = $this->input->post('tv_tag');
        $id = md5($author . date("Y-m-d h:i:sa"));
        $date = $this->input->post("tv_date");
        $article = $this->input->post("tv_article");
        $image = '';
        $play = 0;
        $like = 0;
        $cmt = 0;
        $type = 0; // 0是待审核，1是审核通过
        if (!empty($_FILES['tv_file']['tmp_name'])) {
            $fileType = substr($_FILES["tv_file"]["type"], 6); //获取image/后面的文件类型
            $fileName = md5($id . date("Y-m-d h:i:sa")) . '.' . $fileType;
            //===============================================
            move_uploaded_file(
                $_FILES["tv_file"]["tmp_name"], //重要！！！！
                './src/data/' . $author . '/video/' . $fileName
            );
            $path = $fileName;
        }
        if (!empty($_FILES['tv_cover']['tmp_name'])) {
            $fileType = substr($_FILES["tv_cover"]["type"], 6); //获取image/后面的文件类型
            $fileName = $id . '.' . $fileType;
            $savepath = './src/data/' . $author . '/img/';
            move_uploaded_file($_FILES['tv_cover']['tmp_name'], $savepath . $fileName);
            $source =  $savepath . $fileName; //原图文件名
            $dst_img = $fileName; //保存图片的文件名
            $percent = 0.3;  #原图压缩，不缩放，但体积大大降低
            $image = (new imgcompress($source, $percent))->compressImg($dst_img, $savepath);move_uploaded_file($_FILES['tv_cover']['tmp_name'], $savepath . $fileName);
            $source =  $savepath . $fileName; //原图文件名
            $dst_img = $fileName; //保存图片的文件名
            $percent = 0.5;  #原图压缩，不缩放，但体积大大降低
            $image = (new imgcompress($source, $percent))->compressImg($dst_img, $savepath);
            //===============================================
            // move_uploaded_file(
            //     $_FILES["tv_cover"]["tmp_name"], //重要！！！！
            //     './src/data/' . $author . '/img/' . $fileName
            // );
            $image = $fileName;
        }
        $result = $this->tutorial_model->uploadVideo($id, $name, $author, $path, $image, $play, $like, $cmt, $date, $tag, $type, $article);
        echo $result;
    }

    public function getVideo () {
        // 搜索某个具体视频，具体信息
        $tv_id = $this->input->post("tv_id");
        $this->db->select('gd_tv.*,gd_user.user_name,gd_user.user_image'); //查询的字段
        $this->db->from('gd_tv'); //连表的主表
        $this->db->where("gd_tv.tv_id = '$tv_id'");
        $this->db->join('gd_user', ' gd_tv.tv_author =  gd_user.user_id', 'left'); //连接表
        $this->db->where(" gd_tv.tv_author = gd_user.user_id"); //查询的条件
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            foreach ($result as &$row) {
                // 循环每一个动态
                // 1.搜索用户头像
                $userimage = './src/data/' . $row['tv_author'] . '/img/' . $row['user_image'];
                if (@fopen($userimage, 'r'))   //如果能打开照片
                {
                    $userimageType = explode(".", $row['user_image'])[1];
                    $userimagestr = file_get_contents($userimage); //把图片读取成字符串
                    $userimageBase64 = base64_encode($userimagestr);
                    $row['user_image'] = 'data:image/' . $userimageType . ';base64,' . $userimageBase64;
                } else {
                    $row['user_image'] = '';
                }
            }
            echo json_encode(array('code' => 1, 'msg' => '为您找到' . count($result) . '条数据', 'data' => $result));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '为您找到' . count($result) . '条数据', 'data' => $result));
        }
    }

    public function searchTutorial()
    {
        $page = $this->input->post("page");
        $limit = $this->input->post("limit");
        $result = $this->tutorial_model->searchTutorial($page, $limit);
        echo $result;
    }

    public function updatePlayTime(){
        $id = $this->input->post("tv_id");
        $this->tutorial_model->updatePlayTime($id);
    }

    // 视频评论
    public function sendComment () {
        $tv_id = $this->input->post('tv_id'); // 动态id
        $tv_owner_id = $this->input->post('tv_owner_id'); // 动态发送者id
        $cmted_id = $this->input->post('cmted_id'); // 被评论人id(发送人或其他回复人)
        $cmt_id = $this->input->post('cmt_id'); // 本条回复发送人id
        $cmt_body = $this->input->post('cmt_body');
        $date = $this->input->post('cmt_date');
        $id = $this->input->post('id');
        $this->db->set('tv_cmt', 'tv_cmt+1', FALSE);
        $this->db->where('tv_id', $tv_id);
        $this->db->update('gd_tv');
        $data = array (
            'id'=>$id,
            'tv_id'=>$tv_id,
            'tv_owner_id'=>$tv_owner_id,
            'cmted_id'=>$cmted_id,
            'cmt_id'=>$cmt_id,
            'cmt_body'=>$cmt_body,
            'date'=>$date,
            'type'=>'cmt',
            'status'=>0
        );
        $result = $this->tutorial_model->sendCommentModel($data);
        echo $result;
    }

    public function getComment () {
        $tv_id = $this->input->post('tv_id');
        $result = $this->tutorial_model->getCommentModel($tv_id);
        echo $result;
    }

    public function deleteComment () {
        $id = $this->input->post('id');
        $tv_id = $this->input->post('tv_id');
        $this->db->where('id', $id);
        $result = $this->db->delete('gd_tv_cmt');
        // 更新评论数
        $this->db->set('tv_cmt', 'tv_cmt-1', FALSE);
        $this->db->where('tv_id', $tv_id);
        $this->db->update('gd_tv');
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '删除成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '删除失败', 'data' => ''));
        }
    }
}
