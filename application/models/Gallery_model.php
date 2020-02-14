<?php
class Gallery_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }
    public function getAllPicModel()
    {
        $result = $this->db->query("SELECT * FROM gd_pic");
        $result = $result->result_array();
        if ($result) {
            return json_encode(array('code' => 1, 'msg' => '为您找到' . count($result) . '条数据', 'data' => $result));
        } else {
            return json_encode(array('code' => 1, 'msg' => '为您找到' . count($result) . '条数据', 'data' => ''));
        }
    }
    // public function searchPic($key,$page,$limit)
    // {
    //     if ($key!=null && $key!='') {
    //         $result = $this->db->query("SELECT * FROM gd_pic WHERE pic_tag like '%$key%' limit ". ($page-1) * $limit . "," . $limit);
    //     } else {
    //         $result = $this->db->query("SELECT * FROM gd_pic limit ". ($page-1) * $limit . "," . $limit);
    //     }
    //     $result = $result->result_array();
    //     if ($key!=null && $key!='') {
    //         $this->db->like("pic_tag",$key);
    //     }
    //     $all = count($this->db->get("gd_pic")->result_array());
    //     if (count($result)>0) {
    //         return json_encode(array('code' => 1, 'msg' => '查找成功', 'data' => $result, 'count' => count($result), 'countAll'=>$all));
    //     } else {
    //         return json_encode(array('code' => 0, 'msg' => '查找失败', 'data' => $result, 'count' => count($result)));
    //     }
    // }

    public function searchPic($search,$page,$limit){
        if ($search!=null && $search!='') {
            $result = $this->db->query("SELECT * FROM gd_pic WHERE pic_tag like '%$search%' limit ". ($page-1) * $limit . "," . $limit);
        } else { 
            $result = $this->db->query("SELECT * FROM gd_pic limit ". ($page-1) * $limit . "," . $limit);
        }
        $result = $result->result_array();
        if ($search!=null && $search!='') {
            $this->db->like("pic_tag",$search);
        }
        $all = count($this->db->get("gd_pic")->result_array());
        if (count($result)>0) {
            return json_encode(array('code' => 1, 'msg' => '查找成功', 'data' => $result, 'count' => count($result), 'countAll'=>$all));
        } else {
            return json_encode(array('code' => 1, 'msg' => '查找失败', 'data' => $result, 'count' => count($result)));
        }
    }
}
