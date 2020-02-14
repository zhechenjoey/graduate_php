<?php
// 推荐系统
class Rs extends CI_Controller
{
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->model('rs_model');
        $this->load->helper('url_helper');
    }
    public function rsvideo()
    {
        // 最新视频
        $search = $this->input->post("searchkey");
        $page = 1;
        $limit = $this->input->post("limit");
        $result = $this->rs_model->rsvideo($search, $page, $limit);
        echo $result;
    }

    public function rsartist()
    {
        // 热门摄影师
        $this->db->limit(6);
        $this->db->where('user_role = 1');
        $this->db->order_by('user_fans', 'DESC');
        $this->db->select('user_id, user_name, user_image, user_tag, user_explain, user_follow, user_fans');
        $result = $this->db->get('gd_user')->result_array();
        if (count($result) > 0) {
            foreach ($result as &$row) {
                // 循环每一个动态
                // 1.搜索用户头像
                $userimage = './src/data/' . $row['user_id'] . '/img/' . $row['user_image'];
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
            echo json_encode(array('code' => 1, 'msg' => '为你查找到' . count($result) . '条数据', 'data' => $result));
        } else {
            echo json_encode(array('code' => 1, 'msg' => '为你查找到' . count($result) . '条数据', 'data' => ''));
        }
    }

    public function rsarticle()
    {
        // 推荐 tv_path 为none的资源：为文章教程
        $this->db->limit(6);
        $this->db->where("tv_path", null);
        $this->db->order_by('tv_date', 'DESC');
        $result = $this->db->get('gd_tv')->result_array();
        if (count($result) > 0) {
            echo json_encode(array('code' => 1, 'msg' => '为你查找到' . count($result) . '条数据', 'data' => $result));
        } else {
            echo json_encode(array('code' => 1, 'msg' => '为你查找到' . count($result) . '条数据', 'data' => ''));
        }
    }

    public function rsaction()
    {
        $this->db->limit(2);
        $this->db->order_by('act_send_date', 'DESC');
        $result = $this->db->get('gd_act')->result_array();
        if (count($result) > 0) {
            echo json_encode(array('code' => 1, 'msg' => '为你查找到' . count($result) . '条数据', 'data' => $result));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '为你查找到' . count($result) . '条数据', 'data' => ''));
        }
    }
}
