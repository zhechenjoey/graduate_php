<?php
class Tutorial_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function uploadVideo($id, $name, $author, $path, $image, $play, $like, $cmt, $date, $tag, $type, $article)
    {
        $data = array('tv_id' => $id, 'tv_name' => $name, 'tv_author' => $author, 'tv_path' => $path, 'tv_cover' => $image, 'tv_play' => $play, 'tv_like' => $like, 'tv_cmt' => $cmt, 'tv_date' => $date, 'tv_tag' => $tag, 'tv_type' => $type, 'tv_article' => $article);
        $result = $this->db->insert('gd_tv', $data);
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => 'upload ok', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => 'upload fail', 'data' => ''));
        }
    }

    public function getVideoList($search,$page,$limit)
    {
    //     if ($search) {
    //         $this->db->like("tv_name", $search);
    //     }
    //    $this->db->order_by('tv_date','DESC');
    //    // $this->db->limit($limit, $page);
    //     $result = $this->db->get("gd_tv");
        if ($search!=null && $search!='') {
            $result = $this->db->query("SELECT * FROM gd_tv WHERE tv_name like '%$search%' ORDER BY tv_date DESC limit ". ($page-1) * $limit . "," . $limit);
        } else {
            $result = $this->db->query("SELECT * FROM gd_tv ORDER BY tv_date DESC limit ". ($page-1) * $limit . "," . $limit);
        }
        $result = $result->result_array();
        if ($search!=null && $search!='') {
            $this->db->like("tv_name",$search);
        }
        $all = count($this->db->get("gd_tv")->result_array());
        if (count($result)>0) {
            foreach ($result as &$row) {
                // 循环搜索封面图
                $userimage = './src/data/' . $row['tv_author'] . '/img/' . $row['tv_cover'];
                if (@fopen($userimage, 'r'))   //如果能打开照片
                {
                    $userimageType = explode(".", $row['tv_cover'])[1];
                    $userimagestr = file_get_contents($userimage); //把图片读取成字符串
                    $userimageBase64 = base64_encode($userimagestr);
                    $row['tv_cover'] = 'data:image/' . $userimageType . ';base64,' . $userimageBase64;
                } else {
                    $row['tv_cover'] = '';
                }
            }
            return json_encode(array('code' => 1, 'msg' => '查找成功', 'data' => $result, 'count' => count($result), 'countAll'=>$all));
        } else {
            return json_encode(array('code' => 1, 'msg' => '查找失败', 'data' => $result, 'count' => count($result)));
        }
    }
    // public function searchTutorial($page, $limit)
    // {
    //     $result = $this->db->get('gd_tv', $page, $limit);
    //     $result = $result->row_array;
    //     if ($result) {
    //         return json_encode(array('code' => 1, 'msg' => '查找成功', 'data' => $result));
    //     } else {
    //         return json_encode(array('code' => 0, 'msg' => '查找', 'data' => $result));
    //     }
    // }
    public function updatePlayTime($id)
    {
        $this->db->set('tv_play', 'tv_play+1', FALSE);
        $this->db->where('tv_id', $id);
        $this->db->update('gd_tv');
    }

    public function sendCommentModel($data)
    {
        $result = $this->db->insert('gd_tv_cmt', $data);
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '回复成功', 'data' => ''));
        } else {
            return json_encode(array('code' => 0, 'msg' => '回复失败', 'data' => ''));
        }
    }

    public function getCommentModel($tv_id)
    {
        $result = $this->db->query(" SELECT * FROM gd_tv_cmt WHERE tv_id = '$tv_id' ");
        $this->db->select('gd_tv_cmt.*, gd_user.user_name, gd_user.user_image'); //查询的字段
        $this->db->from('gd_tv_cmt'); //连表的主表
        $this->db->where("gd_tv_cmt.tv_id = '$tv_id' and gd_tv_cmt.type='cmt' ");
        $this->db->join('gd_user', ' gd_tv_cmt.cmt_id =  gd_user.user_id', 'left'); //连接表
        // $this->db->join('gd_user', ' gd_tv_cmt.cmted_id =  gd_user.user_id', 'left'); //连接表
        $this->db->where(" gd_tv_cmt.cmt_id = gd_user.user_id"); //查询的条件
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
}
