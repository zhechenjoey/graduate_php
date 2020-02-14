<?php
class Artist extends CI_Controller
{
    public function __construct()
    {
        session_start();
        parent::__construct();
        $this->load->database();

        // $this->load->model('artist_model');
        $this->load->helper('url_helper');
    }
    public function searchArtist () {
        // 搜索用户
        $key = $this->input->post("key");
        $result = $this->db->query("SELECT user_id,user_name,user_gender,user_role,user_explain,user_image,user_follow,user_fans,user_tag  FROM gd_user WHERE '$key' like user_id  or '$key' like user_name")->result_array();
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
            echo json_encode(array('code' => 1, 'msg' => '为你查找到' . count($result) . '位用户', 'data' => $result));
        } else {
            echo json_encode(array('code' => 1, 'msg' => '未查询到该用户', 'data' => ''));
        }
    }
    public function getTopArtist()
    {
        $this->db->limit(8);
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

    public function getNearbyArtist()
    {
        $city = $this->input->post("city");
        $this->db->limit(8);
        $this->db->like('user_city', $city);
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

    public function checkFollow () {
        $followed_id = $this->input->post("followed_id");
        $follow_id = $this->input->post("follow_id");
        $this->db->where('followed_id',$followed_id);
            $this->db->where('follow_id',$follow_id);
            $result = $this->db->get('gd_follow')->result_array();
            if(count($result)>0){
                echo json_encode(array('code' => 1, 'msg' => '关注了此用户', 'data' => ''));
            } else {
                echo json_encode(array('code' => 0, 'msg' => '未关注此用户', 'data' => ''));
            }
    }
    public function follow()
    {
        // 关注
        $followed_id = $this->input->post("followed_id");
        $follow_id = $this->input->post("follow_id");
        $data = array(
            'followed_id' => $followed_id,
            'follow_id' => $follow_id
        );
        $result = $this->db->query("SELECT * FROM gd_follow WHERE followed_id = $followed_id AND follow_id = $follow_id")->result_array();
        if (count($result) > 0) { //说明已经关注过了
            return ;
        } else {
            $this->db->insert('gd_follow',$data);
            /* 被关注者，粉丝 + 1 */
            $this->db->set('user_fans', 'user_fans+1', FALSE);
            $this->db->where('user_id', $followed_id);
            $this->db->update('gd_user');
            /* 关注者，关注 + 1 */
            $this->db->set('user_follow', 'user_follow+1', FALSE);
            $this->db->where('user_id', $follow_id);
            $this->db->update('gd_user');
            echo json_encode(array('code' => 1, 'msg' => '关注成功', 'data' => $result));
        }
    }
    public function unfollow()
    {
        $followed_id = $this->input->post("followed_id");
        $follow_id = $this->input->post("follow_id");
        $result = $this->db->query("SELECT * FROM gd_follow WHERE followed_id = $followed_id AND follow_id = $follow_id")->result_array();
        if (count($result) == 0) { //说明没关注
            echo json_encode(array('code' => 0, 'msg' => '取关失败，你还未关注此用户', 'data' => ''));
        } else {
            $this->db->where('followed_id',$followed_id);
            $this->db->where('follow_id',$follow_id);
            $result = $this->db->delete('gd_follow');
            /* 被关注者，粉丝 - 1 */
            $this->db->set('user_fans', 'user_fans-1', FALSE);
            $this->db->where('user_id', $followed_id);
            $this->db->update('gd_user');
            /* 关注者，关注 - 1 */
            $this->db->set('user_follow', 'user_follow-1', FALSE);
            $this->db->where('user_id', $follow_id);
            $this->db->update('gd_user');
            if($result){
                echo json_encode(array('code' => 1, 'msg' => '取关成功', 'data' => $result));
            } else {
                echo json_encode(array('code' => 0, 'msg' => '取关失败', 'data' => ''));
            }
        }
    }

}
