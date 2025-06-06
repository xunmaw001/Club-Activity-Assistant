<?php
session_start();
class ToupiaoxinxiController extends CommonController {

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header('Access-Control-Allow-Headers:Origin,Content-Type,Accept,token,X-Requested-With,device');
    }
    public $columData = [
		'id','addtime'
                ,'toupiaobianhao'
                ,'toupiaobiaoti'
                ,'fengmian'
                ,'zhanghao'
                ,'toupiaoshijian'
                ,'toupiaojieguo'
                ,'beizhu'
                ,'crossuserid'
                ,'crossrefid'
                ,'clicktime'
                ,'clicknum'
                ,'userid'
            ];
        /**
     * 分页，列表
     * get
     */
    public function page(){
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>403,'msg'=>"你还没有登录。"]));
        $userid = $tokens['id'];
		$where = " where 1 ";//查询条件
                                                                                if($tokens['isAdmin'] != 1 && $tokens['tablename'] == 'yonghu') {
            $where .= " and `zhanghao` = '".$tokens['username']."' ";
        }
                                                                                                                                                                                                                                                                                                                                                                				
        $page = isset($_REQUEST['page'])?$_REQUEST['page']:"1";
        $limt = isset($_REQUEST['limit'])?$_REQUEST['limit']:"10";
        $sort = isset($_REQUEST['sort'])?$_REQUEST['sort']:"id";
        $order = isset($_REQUEST['order'])?$_REQUEST['order']:"asc";
        foreach ($_REQUEST as $k => $val){
			if(in_array($k, $this->columData)){
                if ($val != ''){
                    $where.= " and ".$k." like '".$val."'";
                }
			}
        }
                                                                                        $base = json_decode(base64_decode($token),true);
        if ($base['isAdmin']!=1){
            $md5 = md5($userid."+10086");
            $colum = "zhanghao";
            $columData = $base['columData'];
            if (isset($_SESSION[$md5]) && in_array($colum, $columData)){
                if($base['tablename'] == 'yonghu'){
                    $where .= " and `zhanghao` = '".$_SESSION[$md5]."'";
                }
            }
        }
                                                                                                                                                        		        
                                        $sql = "select * from `toupiaoxinxi` ".$where;
        $count = table_sql($sql);
        if ($count->num_rows < 1){
            $numberCount = 0;
        }else{
            $numberCount = $count->num_rows;
        }
        $page_count = ceil($numberCount/$limt);//页数
        $startCount = ($page-1)*$limt;
                    $lists = "select * from `toupiaoxinxi` ".$where." order by ".$sort." ".$order." limit ".$startCount.",".$limt;
                
        
        $result = table_sql($lists);
        $arrayData = array();
        if ($result->num_rows > 0) {
            while ($datas = $result->fetch_assoc()){
                array_push($arrayData,$datas);
            }
        }
        exit(json_encode([
            'code'=>0,
            'data' => [
                "total" => $numberCount,
                "pageSize" => $limt,
                "totalPage" => $page_count,
                "currPage" => $page,
                "list" => $arrayData
            ]
        ]));
		    }
    /**
     * 分页，列表list
     * get
     */
    public function lists(){
                $page = isset($_REQUEST['page'])?$_REQUEST['page']:"1";
        $limt = isset($_REQUEST['limit'])?$_REQUEST['limit']:"10";
        $sort = isset($_REQUEST['sort'])?$_REQUEST['sort']:"id";
        $order = isset($_REQUEST['order'])?$_REQUEST['order']:"asc";
        $refid = isset($_REQUEST['refid']) ? $_REQUEST['refid'] : "0";
		$where = " where 1 ";//查询条件
                                                                                if($tokens['isAdmin'] != 1 && $tokens['tablename'] == 'yonghu') {
            $where .= " and `zhanghao` = '".$tokens['username']."' ";
        }
                                                                                                                                                                                                                                                                                                                                                                				foreach ($_REQUEST as $k => $val){
			if(in_array($k, $this->columData)){
				$where.= " and ".$k." like '".$val."'";
			}
        }
		                $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>403,'msg'=>"你还没有登录。"]));
        $userid = $tokens['id'];

                
                                                                                                $token = $this->token();
        $base = json_decode(base64_decode($token),true);
        $userid = $base['id'];
        if ($base['isAdmin']!=1){
            $md5 = md5($userid."+10086");
            $colum = "zhanghao";
            $columData = $base['columData'];
            if (isset($base['success'][$colum]) && in_array($colum, $columData)){
                $where .= " and `zhanghao` = '".$base['success'][$colum]."'";
            }
        }
                                                                                                                                                        		                                $sql = "select * from `toupiaoxinxi`".$where;
        $count = table_sql($sql);
        if ($count->num_rows < 1){
            $numberCount = 0;
        }else{
            $numberCount = $count->num_rows;
        }
        $page_count = ceil($numberCount/$limt);//页数
        $startCount = ($page-1)*$limt;
        $lists = "select * from `toupiaoxinxi` ".$where." order by ".$sort." ".$order." limit ".$startCount.",".$limt;
        $result = table_sql($lists);
        $arrayData = array();
        if ($result->num_rows > 0) {
            while ($datas = $result->fetch_assoc()){
                array_push($arrayData,$datas);
            }
        }
        exit(json_encode([
            'code'=>0,
            'data' => [
                "total" => $numberCount,
                "pageSize" => $limt,
                "totalPage" => $page_count,
                "currPage" => $page,
                "list" => $arrayData
            ]
        ]));
		    }
    
    

    /**
     * 新增数据接口
     * post
     */
    public function save(){
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>403,'msg'=>"你还没有登录。"]));
        $uid = $tokens['id'];
                $keyArr = $valArr = array();
        $tmpData = strval(file_get_contents("php://input"));//Content-Type: application/json 需要用到php://input 处理输入流
        
        if (!empty($tmpData)&& isset($tmpData)){
            $postData = json_decode($tmpData,true);
												$postData['userid'] = $uid;
							            foreach ($postData as $key => $value){
                if (in_array($key, $this->columData)){
                    if(!empty($value) || $value == 0) {
                        if ($key == 'id') {
                            continue;
                        }
                        array_push($keyArr,"`".$key."`");
                        if($key == 'clicktime') {
                            array_push($valArr,"'".date('Y-m-d h:i:s', time())."'");
                        } else {
                            array_push($valArr,"'".$value."'");
                        }
                    }
                }
            }
        }
                $k = implode(',',$keyArr);
        $v = implode(',',$valArr);
                        $sql = "INSERT INTO `toupiaoxinxi` (".$k.") VALUES (".$v.")";
                $result = table_sql($sql);
		        exit(json_encode(['code'=>0]));
    }
    /**
     * 新增数据接口 add
     * post
     */
    public function add(){
        $keyArr = $valArr = array();
		                $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>403,'msg'=>"你还没有登录。"]));
        $uid = $tokens['id'];
                        $tmpData = strval(file_get_contents("php://input"));
        if (!empty($tmpData)&& isset($tmpData)){
            $postData = json_decode($tmpData,true);
							                    $token = $this->token();
                    $tokens = json_decode(base64_decode($token),true);
                    if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>403,'msg'=>"你还没有登录。"]));
                    $uid = $tokens['id'];
					$postData['userid'] = $uid;
							            foreach ($postData as $key => $value){
                if (in_array($key, $this->columData)){
                    if(!empty($value) || $value == 0) {
                        if ($key == 'id') {
                            continue;
                        }
                        array_push($keyArr,"`".$key."`");
                        if($key == 'clicktime') {
                            array_push($valArr,"'".date('Y-m-d h:i:s', time())."'");
                        } else {
                            array_push($valArr,"'".$value."'");
                        }
                    }
                }
            }
        }
        $k = implode(',',$keyArr);
        $v = implode(',',$valArr);
                $sql = "INSERT INTO `toupiaoxinxi` (".$k.") VALUES (".$v.")";
        $result = table_sql($sql);
		        exit(json_encode(['code'=>0]));
    }
    /**
     * 更新接口
     * post
     */
    public function update(){
        $tmpData = strval(file_get_contents("php://input"));
        $postData = json_decode($tmpData,true);
        $v = array();
        foreach ($postData as $key => $value){
            if (in_array($key, $this->columData)){
                if ($key == "id"){
                    $id = $value;
                }
                if(!empty($value) || $value === 0) {
                    array_push($v,$key." = '".$value."'");
                }
            }
        }
        $value = implode(',',$v);
         $sql = "UPDATE toupiaoxinxi SET ".$value." where id = ".$id;
        $result = table_sql($sql);
        exit(json_encode(['code'=>0]));
    }
    /**
     * 删除
     * post
     */
    public function delete(){
        $ids = strval(file_get_contents("php://input"));//发现接收的是字符串
        preg_match_all('/\d+/',$ids,$arr);
        $str = implode(',',$arr[0]);//拼接字符，
        $sql = "delete from toupiaoxinxi WHERE id in({$str})";
        $result = table_sql($sql);
        exit(json_encode(['code'=>0]));
    }
    /**
     * 查询一条数据
     * get
     */
    public function info($id=false){

        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>403,'msg'=>"你还没有登录。"]));
        $userid = $tokens['id'];
        $name = isset($_REQUEST['name'])? $_REQUEST['name']:"";
        if (!empty($id)){
            $where = "`id` = ".$id;
        }else{
            $where = "`name` = ".$name;
        }
                $this->andone('toupiaoxinxi',$id,$column='clicknum',$num=1);
                        $clicktime = "update toupiaoxinxi set clicktime = ".date('Y-m-d H:i:s',time())." where ".$where;
        table_sql($clicktime);
                $sql = "select * from `toupiaoxinxi` where ".$where;
        $result = table_sql($sql);
        if ($result->num_rows > 0) {
            // 输出数据
            while($row = $result->fetch_assoc()) {
                $lists = $row;
            }
        }
        exit(json_encode([
            'code'=>0,
            'data'=> $lists
        ]));
    }
    /**
     * 查询一条数据
     * get
     */
    public function detail($id=false){
        
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>403,'msg'=>"你还没有登录。"]));
        $userid = $tokens['id'];
                $name = isset($_REQUEST['name'])? $_REQUEST['name']:"";
        if ($id){
            $where = "`id` = ".$id;
        }else{
            $where = "`name` = ".$name;
        }
                $this->andone('toupiaoxinxi',$id,$column='clicknum',$num=1);
                        $clicktime = "update toupiaoxinxi set clicktime = '".date('Y-m-d H:i:s',time())."' where ".$where;
        table_sql($clicktime);
                $sql = "select * from `toupiaoxinxi` where ".$where;
        $result = table_sql($sql);
        if (!$result) exit(json_encode(['code'=>500,'msg'=>"查询数据发生错误。"]));
        if ($result->num_rows > 0) {
            // 输出数据
            while($row = $result->fetch_assoc()) {
                $lists = $row;
            }
        }
        exit(json_encode([
            'code'=>0,
            'data'=> $lists
        ]));
    }
            
        public function autoSort(){
		$limt = isset($_REQUEST['limit'])?$_REQUEST['limit']:"5";
				$sort = 'clicknum';
				$lists = "select * from `toupiaoxinxi` order by ".$sort." desc limit 0,".$limt;
		$result = table_sql($lists);
		$arrayData = array();
		if ($result->num_rows > 0) {
			while ($datas = $result->fetch_assoc()){
				array_push($arrayData,$datas);
			}
		}
		exit(json_encode([
			'code'=>0,
			'data' => [
				"total" => 0,
				"pageSize" => $limt,
				"totalPage" => 0,
				"currPage" => 0,
				"list" => $arrayData
			]
		]));
	}
    
    /**
     * 获取需要提醒的记录数接口
     * get
     */
    public function remind($columnName,$type){
        $remindStart = isset($_GET['remindstart'])?$_GET['remindstart']:"";
        $remindEnd = isset($_GET['remindend'])?$_GET['remindend']:"";
        $where = '';
                                    $token = $this->token();
                $tokens = json_decode(base64_decode($token),true);
                if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>403,'msg'=>"你还没有登录。"]));
                $uid = $tokens['id'];
                $where .= " and `userid` = '".$uid."'";
                            if ($type == 1){//数字
            if ($remindStart && $remindEnd){
                $where .= " and ".$columnName."<='".$remindEnd."' and ".$columnName.">='".$remindStart."'";
            }elseif($remindStart){
                $where .= " and ".$columnName.">='".$remindStart."'";
            }elseif($remindEnd){
                $where .= " and ".$columnName."<='".$remindEnd."'";
            }
        }else{
            if ($remindStart && $remindEnd){
                $where .= " and ".$columnName."<='".date("Y-m-d",strtotime("+".$remindEnd." day"))."' and ".$columnName.">='".date("Y-m-d",strtotime("+".$remindStart." day"))."'";
            }elseif($remindStart){
                $where .= " and ".$columnName.">='".date("Y-m-d",strtotime("+".$remindStart." day"))."'";
            }elseif($remindEnd){
                $where .= " and ".$columnName."<='".date("Y-m-d",strtotime("+".$remindEnd." day"))."'";
            }
            
        }
        $sql = "select * from `toupiaoxinxi` where 1 ".$where;
        $result = table_sql($sql);
        exit(json_encode(['code'=> 0 ,'count' => $result->num_rows]));
    }

    }

