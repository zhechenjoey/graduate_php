<?php
class Rs_model extends CI_Model {
    public function __construct()
    {
        $this->load->database();
    }
    public function rsvideo($search,$page,$limit)
    {
        if($search==''){
            $result = $this->db->query("SELECT * FROM gd_tv ORDER BY tv_date DESC limit ". ($page-1) * $limit . "," . $limit); 
        } else {
        $result = $this->db->query("SELECT * FROM gd_tv WHERE tv_tag like '%$search%' ORDER BY tv_date DESC limit ". ($page-1) * $limit . "," . $limit);     
        }
        $result = $result->result_array();
        $all = count($this->db->get("gd_tv")->result_array());
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
        if (count($result)>0) {
            return json_encode(array('code' => 1, 'msg' => '查找成功', 'data' => $result, 'count' => count($result), 'countAll'=>$all));
        } else {
            return json_encode(array('code' => 0, 'msg' => '查找失败', 'data' => $result, 'count' => count($result)));
        }
    }
    public function rsaction(){
        
    }
}

?>