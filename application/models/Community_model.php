<?php
header('Content-Type:application/json; charset=utf-8');
class Community_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }
    public function sendMood($mood_id, $user_id, $mood_body, $mood_date, $mood_pic, $mood_tag)
    {
        // 更新 mood 表
        $data = array('mood_id' => $mood_id, 'user_id' => $user_id, 'mood_body' => $mood_body, 'mood_date' => $mood_date, 'mood_pic' => $mood_pic, 'mood_tag' => $mood_tag);
        $result = $this->db->insert('gd_mood', $data);
        // 更新 type 表
        if ($mood_tag !== '' && $mood_tag !== null) {
            $typeArray = explode(";", $mood_tag);
            foreach ($typeArray as &$row) {
                $searchType = $this->db->query("SELECT * FROM gd_type WHERE type_name = '$row' ")->row_array();
                if ($searchType) {
                    // 此类型已经在数据库中
                    $this->db->set('type_time', 'type_time+1', FALSE);
                    $this->db->where('type_name', $row);
                    $this->db->update('gd_type');
                } else {
                    $typeData = array('type_name' => $row, 'type_time' => '1');
                    $this->db->insert('gd_type', $typeData);
                }
            }
        }

        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '上传成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '上传失败', 'data' => ''));
        }
    }
    public function sendpic($imgName, $user_id, $like, $tag)
    {
        $data = array('pic_id' => $imgName, 'pic_user_id' => $_SESSION['vchenzhecom'], 'pic_like' => $like, 'pic_tag' => $tag);
        $this->db->insert('gd_pic', $data);
    }
    public function getMood()
    {
        // $result = $this->db->query('SELECT * FROM gd_mood');
        // $result = $result->result_array();
        $this->db->select('gd_mood.mood_id,gd_mood.mood_body,gd_mood.mood_date,gd_mood.mood_pic,gd_mood.mood_tag,gd_user.user_id,gd_user.user_name,gd_user.user_image'); //查询的字段

        //$this->db->where("mood.user_id = user.user_id"); //查询的条件

        $this->db->from('gd_mood'); //连表的主表

        $this->db->join('gd_user', ' gd_mood.user_id =  gd_user.user_id', 'left'); //连接表
        $this->db->order_by('mood_date', 'DESC');
        $result = $this->db->get()->result_array(); //语句查询，切记->result_array()；必须的有
        if ($result) {
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
                // 2.搜索动态里的图片
                $picArray = explode(';', $row['mood_pic']);
                for ($p = 0; $p < sizeof($picArray); $p++) {
                    //每次只搜索第一张图片
                    $imgPic = './src/data/' . $row['user_id'] . '/img/' . $picArray[$p];
                    if (@fopen($imgPic, 'r'))   //如果能打开照片
                    {
                        $imgType = explode(".", $picArray[$p])[1];
                        $imgstr = file_get_contents($imgPic); //把图片读取成字符串
                        $imgBase64 = base64_encode($imgstr);
                        $picArray[$p] = 'data:image/' . $imgType . ';base64,' . $imgBase64;
                    } else {
                        //$picArray[$p]='';
                    }
                }
                array_pop($picArray);
                $row['mood_pic'] = $picArray;
            }
            return json_encode(array('code' => 1, 'msg' => '查找成功', 'data' => $result));
        } else {
            return json_encode(array('code' => 0, 'msg' => '未查找到结果', 'data' => ''));
        }
    }

    public function getLatest()
    {
        $this->db->limit(1);
        $this->db->select('gd_mood.mood_id,gd_mood.mood_body,gd_mood.mood_date,gd_mood.mood_pic,gd_mood.mood_tag,gd_user.user_id,gd_user.user_name,gd_user.user_image'); //查询的字段

        $this->db->from('gd_mood'); //连表的主表
        $this->db->join('gd_user', ' gd_mood.user_id =  gd_user.user_id', 'left'); //连接表
        $this->db->where("gd_mood.user_id = gd_user.user_id"); //查询的条件
        $this->db->order_by('mood_date', 'DESC');

        $result = $this->db->get()->row_array(); //语句查询，切记->result_array()；必须的有
        if ($result) {
            // 循环每一个动态
            // 1.搜索用户头像
            $userimage = './src/data/' . $result['user_id'] . '/img/' . $result['user_image'];
            if (@fopen($userimage, 'r'))   //如果能打开照片
            {
                $userimageType = explode(".", $result['user_image'])[1];
                $userimagestr = file_get_contents($userimage); //把图片读取成字符串
                $userimageBase64 = base64_encode($userimagestr);
                $result['user_image'] = 'data:image/' . $userimageType . ';base64,' . $userimageBase64;
            } else {
                $result['user_image'] = '';
            }
            // 2.搜索动态里的图片
            $picArray = explode(';', $result['mood_pic']);
            for ($p = 0; $p < sizeof($picArray); $p++) {
                //每次只搜索第一张图片
                $imgPic = './src/data/' . $result['user_id'] . '/img/' . $picArray[$p];
                if (@fopen($imgPic, 'r'))   //如果能打开照片
                {
                    $imgType = explode(".", $picArray[$p])[1];
                    $imgstr = file_get_contents($imgPic); //把图片读取成字符串
                    $imgBase64 = base64_encode($imgstr);
                    $picArray[$p] = 'data:image/' . $imgType . ';base64,' . $imgBase64;
                } else {
                    //$picArray[$p]='';
                }
            }
            array_pop($picArray);
            $result['mood_pic'] = $picArray;

            return json_encode(array('code' => 1, 'msg' => '查找成功', 'data' => $result));
        } else {
            return json_encode(array('code' => 0, 'msg' => '未查找到结果', 'data' => ''));
        }
    }

    public function deleteMood($id)
    {
        $this->db->where('mood_id', $id);
        $result = $this->db->delete('gd_mood');
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '删除成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '删除失败', 'data' => ''));
        }
    }

    public function getCommentModel($mood_id)
    {
        $result = $this->db->query(" SELECT * FROM gd_comment WHERE mood_id = '$mood_id' ");
        $this->db->select('gd_comment.*, gd_user.user_name, gd_user.user_image'); //查询的字段
        $this->db->from('gd_comment'); //连表的主表
        $this->db->where("gd_comment.mood_id = '$mood_id' and gd_comment.type='cmt' ");
        $this->db->join('gd_user', ' gd_comment.cmt_id =  gd_user.user_id', 'left'); //连接表
        // $this->db->join('gd_user', ' gd_comment.cmted_id =  gd_user.user_id', 'left'); //连接表
        $this->db->where(" gd_comment.cmt_id = gd_user.user_id"); //查询的条件
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            foreach ($result as &$row) {
                // 循环每一个动态
                // 1.搜索用户头像
                $userimage = './src/data/' . $row['cmt_id'] . '/img/' . $row['user_image'];
                if (@fopen($userimage, 'r'))   //如果能打开照片
                {
                    $userimageType = explode(".", $row['user_image'])[1];
                    $userimagestr = file_get_contents($userimage); //把图片读取成字符串
                    $userimageBase64 = base64_encode($userimagestr);
                    $row['user_image'] = 'data:image/' . $userimageType . ';base64,' . $userimageBase64;
                } else {
                    $row['user_image'] = '';
                }

                //2.循环搜索 cmted_name
                if ($row['cmted_id']) {
                    $cmted_id = $row['cmted_id'];
                    $re = $this->db->query("SELECT * FROM gd_user WHERE user_id = $cmted_id");
                    $re = $re->row_array();
                    //$result = json_encode($result);
                    $row['cmted_name'] = $re['user_name'];
                }
            }
            return json_encode(array('code' => 1, 'msg' => '为您找到' . count($result) . '条数据', 'data' => $result));
        } else {
            return json_encode(array('code' => 0, 'msg' => '为您找到' . count($result) . '条数据', 'data' => $result));
        }
    }

    public function sendCommentModel($data)
    {
        $result = $this->db->insert('gd_comment', $data);
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '回复成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '回复失败', 'data' => ''));
        }
    }

    public function like($data)
    {
        $result = $this->db->insert('gd_comment', $data);
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '点赞成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '点赞失败', 'data' => ''));
        }
    }
}
