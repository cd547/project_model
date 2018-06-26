<?php
class pro_level1 extends Zend_Db_Table
{

	protected $_name='pro_level1';//表名
	/*
	 * 创建项目
	 * 接收用户提交的数据，将其入录数据表
	 * 接收数据应是数组，必须确保键名和数据表字段一致
	 * @param array $userData
	 */
	function create_project($userData)
	{
		$row=$this->createRow();
		if(count($userData)>0){
		    foreach($userData as $key=>$value)
		    {
		        switch($key)
		        {
		            default:
		                $row->$key=$value;
		        }
		    }
            date_default_timezone_set('PRC'); //设置中国时区
            $time = time();
            $createtime = date("y-m-d H:i:s",$time);
		    $row->pro_createTime=$createtime;
            $row->pro_updateTime=$createtime;
		    $row->save();
		    return $row->id;
		}
		else {
		    return null;
		}
	}
	//更新
	function update_pwd($username,$pwd)
	{
	    $data=array('password'=>md5($pwd));
	    //try
	    {
	        //更新
	        $where=" username='".$username."'";
	        $num=$this->update($data, $where);
	        if($num>0)
	        {
	            return 1;
	        }
	        else {return 0;}
	    }
	}
    //更新显示名
    function update_showname($username, $showname)
    {
        $data=array('showname'=>$showname);
        //try
        {
            //更新
            $where=" username='".$username."'";
            $num=$this->update($data, $where);
            if($num>0)
            {
                return 1;
            }
            else {return 0;}
        }
    }

	//更新email
	function update_email($id,$email)
	{
	    $data=array('email'=>$email);
	    //try
	    {
	        //更新
	        $where=" id='".$id."'";
	        $num=$this->update($data, $where);
	        if($num>0)
	        {
	            return 1;
	        }
	        else {return 0;}
	    }
	}


	//判断用户是否接收短信
	function receive_message($userid)
	{
	    $state=false;
	    $db=$this->getAdapter();
	    $sql = $db->quoteInto('SELECT * FROM users WHERE id = ?',$userid);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {$state=$res[0]['短信提醒'];}//
	    return $state;
	}
	//判断用户是否存在
	function checkusername($username)
	{
	    $state=false;
	    $db=$this->getAdapter();
	    $sql = $db->quoteInto('SELECT username FROM users WHERE username = ?',$username);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {$state=true;}//存在
	    return $state;
	}
	//判断邮箱是否存在
	function checkemail($email)
	{
	    $state=false;
	    $db=$this->getAdapter();
	    $sql = $db->quoteInto('SELECT email FROM users WHERE email = ?',$email);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {$state=true;}//存在
	    return $state;
	}
	//判断cellphone是否存在
	function checkcellphone($cellphone)
	{
	    $state=false;
	    $db=$this->getAdapter();
	    $sql = $db->quoteInto('SELECT cellphone FROM users WHERE cellphone = ?',$cellphone);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {$state=true;}//存在
	    return $state;
	}
	//获取用户
	function getUser($cellphone)
	{
	    $db=$this->getAdapter();
	    $sql=$db->quoteInto("SELECT * FROM users WHERE cellphone=?",$cellphone);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {
	       return $res;
	    }
	    else {
	        return null;
	    }

	}
	//获取用户by工号
	function getUserbycode($code)
	{
	    $db=$this->getAdapter();
	    $sql=$db->quoteInto("SELECT * FROM users WHERE code=?",$code);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {
	       return $res;
	    }
	    else {
	        return null;
	    }

	}
	//获取用户by登录名
	function getUserbyusername($username)
	{
	    $db=$this->getAdapter();
	    $sql=$db->quoteInto("SELECT * FROM users WHERE username=?",$username);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {
	        return $res;
	    }
	    else {
	        return null;
	    }
	}
	
	//记录登录时间
	function loginTime($id)
	{
	   $row=$this->find($id) ->current();
	   if($row){
	       $row->time_last=time();
	       $row->save();
	   }
	   else{
	       return FALSE;
	   }
	}
	//获取部门
    function get_dep($username)
    {
        $db=$this->getAdapter();
        $sql=$db->quoteInto("SELECT * FROM users WHERE username=?",$username);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res[0]['dep'];
        }
        else {
            return null;
        }
    }
	//通过部门获取成员
	function getUsersByDep($dep)
	{
	    $db=$this->getAdapter();
	    //$sql=$db->quoteInto("SELECT * FROM users WHERE dep=? ORDER BY CONVERT(username USING GBK) ASC",$dep);
		$sql=$db->quoteInto("SELECT * FROM users WHERE dep=? ORDER BY xh ASC",$dep);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {
	        return $res;
	    }
	    else {
	        return null;
	    }
	
	}
    //获取email
    function getUserEmail($id)
    {
        $db=$this->getAdapter();
        $sql=$db->quoteInto("SELECT * FROM users WHERE id=?",$id);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res[0]['email'];
        }
        else {
            return null;
        }
    }
    //获取cellphone
    function getUserPhone($id)
    {
        $db=$this->getAdapter();
        $sql=$db->quoteInto("SELECT * FROM users WHERE id=?",$id);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res[0]['cellphone'];
        }
        else {
            return null;
        }
    }
    //获取用户
    function getUserbyId($userid)
    {
        $db=$this->getAdapter();
        $sql=$db->quoteInto("SELECT * FROM users WHERE id=?",$userid);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res;
        }
        else {
            return null;
        }
    }
    
    //获取所有用户
    function getUsers()
    {
        $db=$this->getAdapter();
        $sql=$db->quoteInto("SELECT * FROM users WHERE 1=?",1);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res;
        }
        else {
            return null;
        }
    }
    //获取id
    function getUseridbycode($usercode)
    {
        $db=$this->getAdapter();
        $sql=$db->quoteInto("SELECT * FROM users WHERE code=?",$usercode);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res[0]['id'];
        }
        else {
            return null;
        }
    }
    //获取微信号
    function getweichartbycode($usercode)
    {
        $db=$this->getAdapter();
        $sql=$db->quoteInto("SELECT * FROM users WHERE code=?",$usercode);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res[0]['weichart'];
        }
        else {
            return null;
        }
    }

}
