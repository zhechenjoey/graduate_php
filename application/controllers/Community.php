<?php
header("Content-Type:Application/json;charset=utf8");
defined('BASEPATH') or exit('No direct script access allowed');
include 'CompressPicture.php';
class Community extends CI_Controller
{
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->database();
        $this->load->model('community_model');
        $this->load->helper('url_helper');
    }
    public function sendMood()
    {
        $user_id = $this->input->post('user_id');
        $mood_date = $this->input->post('mood_date');
        $mood_body = $this->input->post('mood_body');
        $mood_tag = $this->input->post('mood_tag');
        $mood_id = md5($user_id . $mood_date);
        $mood_pic = '';
        $savepath = './src/data/' . $user_id . '/img/';
        $i = 0;
        if (!empty($_FILES['mood_pic'])) //使用数组判断当前是否上传了图片
        {
            foreach ($_FILES['mood_pic']['name'] as $key => $value) {
                if (
                    ($_FILES["mood_pic"]["type"][$key] == "image/gif")
                    || ($_FILES["mood_pic"]["type"][$key] == "image/jpeg")
                    || ($_FILES["mood_pic"]["type"][$key] == "image/jpg")
                    || ($_FILES["mood_pic"]["type"][$key] == "image/pjpeg")
                    || ($_FILES["mood_pic"]["type"][$key] == "image/x-png")
                    || ($_FILES["mood_pic"]["type"][$key] == "image/png")
                ) {
                    $imgType = explode('.', $_FILES['mood_pic']['name'][$key])[1];
                    //设置照片的存放相对路径和命名。命名照片例：20161226_2.png，下划线后跟遍历的键值区分照片，可在此处自行设置规则！！
                    $imgName = md5($user_id . $mood_date . '-' . $i) . '.' . $imgType;
                    //$imgName=$_FILES['mood_pic']['name'][$key];
                    //将上传的文件移动到新位置
                    move_uploaded_file($_FILES['mood_pic']['tmp_name'][$key], $savepath . $imgName);
                    $source =  $savepath . $imgName; //原图文件名
                    $dst_img = $imgName; //保存图片的文件名
                    $percent = 0.3;  #原图压缩，不缩放，但体积大大降低
                    $image = (new imgcompress($source, $percent))->compressImg($dst_img, $savepath);
                    $this->community_model->sendpic($imgName,$user_id,0,$mood_tag);
                    //显示出上传的图片
                    $mood_pic = $imgName . ';' . $mood_pic;
                    $i = $i + 1;
                }
            }
        } else {
            $mood_pic = '';
        }
        $result = $this->community_model->sendMood($mood_id, $user_id, $mood_body, $mood_date, $mood_pic, $mood_tag);
        echo $result;
    }
    //获取所有动态
    public function getMood() 
    {
        $result = $this->community_model->getMood();
        echo $result;
    }
    //获取最新的一条动态
    public function getLatest()
    {
        $result = $this->community_model->getLatest();
        echo $result;
    }
    public function deleteMood()
    {
        $id = $this->input->post("id");
        $user_id = $_SESSION['vchenzhecom'];

        $this->db->where('mood_id', $id);
        $picArray = $this->db->get('gd_mood')->row_array();
        // $mood_tag = $this->db->query("SELECT mood_tag FROM gd_mood WHERE mood_id = '$id' ");
        // if ($mood_tag !== '' && $mood_tag !== null) {
        //     $typeArray = explode(";", $mood_tag);
        //     foreach ($typeArray as &$row) {
        //         $searchType = $this->db->query("SELECT * FROM gd_type WHERE type_name = '$row' ")->row_array();
        //         if ($searchType) {
        //             // 此类型已经在数据库中
        //             $this->db->set('type_time', 'type_time-1', FALSE);
        //             $this->db->where('type_name', $row);
        //             $this->db->update('gd_type');
        //         } else {
        //             $typeData = array('type_name' => $row, 'type_time' => '1');
        //             $this->db->insert('gd_type', $typeData);
        //         }
        //     }
        // }
        //echo $picArray['mood_pic'];
        if (count($picArray)) {
            $resultArray = explode(';', $picArray['mood_pic']);
            for ($p = 0; $p < count($resultArray); $p++) {
                $imgPic = './src/data/' . $user_id . '/img/' . $resultArray[$p];
                if (@fopen($imgPic, 'r'))   //如果能打开照片
                    unlink($imgPic);
            }
            $this->community_model->deleteMood($id);
            echo json_encode(array('code' => 1, 'msg' => '删除成功'));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '删除失败'));
        }
    }

    public function getComment () {
        $mood_id = $this->input->post('mood_id');
        $result = $this->community_model->getCommentModel($mood_id);
        echo $result;
    }

    public function sendComment () {
        $mood_id = $this->input->post('mood_id'); // 动态id
        $mood_owner_id = $this->input->post('mood_owner_id'); // 动态发送者id
        $cmted_id = $this->input->post('cmted_id'); // 被评论人id(发送人或其他回复人)
        $cmt_id = $this->input->post('cmt_id'); // 本条回复发送人id
        $cmt_body = $this->input->post('cmt_body');
        $date = $this->input->post('cmt_date');
        $id = $this->input->post('id');
        $data = array (
            'id'=>$id,
            'mood_id'=>$mood_id,
            'mood_owner_id'=>$mood_owner_id,
            'cmted_id'=>$cmted_id,
            'cmt_id'=>$cmt_id,
            'cmt_body'=>$cmt_body,
            'date'=>$date,
            'type'=>'cmt',
            'status'=>0
        );
        $result = $this->community_model->sendCommentModel($data);
        echo $result;
    }

    public function deleteComment () {
        $id = $this->input->post('id');
        $this->db->where('id', $id);
        $result = $this->db->delete('gd_comment');
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '删除成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '删除失败', 'data' => ''));
        }
    }

    public function checklike () {
        $mood_id = $this->input->post('mood_id');
        $cmt_id = $this->input->post('cmt_id');
        $this->db->where('mood_id',$mood_id);
        $this->db->where('cmt_id',$cmt_id);
        $this->db->where('type','like');
        $result = $this->db->get('gd_comment');
        $result = $result->row_array();
        if($result){
            echo json_encode(array('code' => 1, 'msg' => '已经点赞', 'data' => $result));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '未点赞', 'data' => ''));
        }
    }

    public function like () {
        $mood_id = $this->input->post("mood_id");
        $cmt_id = $this->input->post("cmt_id");
        $date = $this->input->post("date");
        $id = md5($mood_id.$cmt_id.$date);
        $data = array (
            'id'=>$id,
            'mood_id'=>$mood_id,
            'cmt_id'=>$cmt_id,
            'date'=>$date,
            'status'=>0,
            'type'=>'like'
        );
        $result = $this->community_model->like($data);
        echo $result;
    }

    public function unlike () {
        $mood_id = $this->input->post("mood_id");
        $cmt_id = $this->input->post("cmt_id");
        //$like_user_id = $this->input->post("like_user_id");
        //$date = $this->input->post("date");
        //$like_id = md5($liked_mood_id.$liked_user_id.$like_user_id.$date);
        $this->db->where('mood_id',$mood_id);
        $this->db->where('cmt_id',$cmt_id);
        $this->db->where('type','like');
        $result = $this->db->delete('gd_comment');
        if($result){
            echo json_encode(array('code' => 1, 'msg' => '删除成功', 'data' => ''));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '删除失败', 'data' => ''));
        }
    }

    public function countlike () {
        $mood_id = $this->input->post("mood_id");
        $this->db->where('mood_id',$mood_id);
        $this->db->where('type','like');
        $result = $this->db->get('gd_comment')->result_array();
        if(count($result)>0){
            echo json_encode(array('code' => 1, 'msg' => '为您找到'.count($result).'条数据', 'data' => count($result)));
        } else {
            echo json_encode(array('code' => 1, 'msg' => '没有人点赞', 'data' => 0));
        }
    }

    public function getType () {
        // 搜索分类列表
        $this->db->order_by("type_time","DESC");
        $this->db->limit(10);
        $result = $this->db->get("gd_type")->result_array();
        echo json_encode(array('code' => 1, 'msg' => '为您找到'.count($result).'条数据', 'data' => $result));
    }
}
