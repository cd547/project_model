<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/users.php';
require_once APPLICATION_PATH.'/models/mail.php';
class UserController extends BaseController
{
    //注册
    public function registerAction()
    {
        $t1 = microtime(true);
        date_default_timezone_set('PRC'); //设置中国时区
        $time = time();
        $starttime = date("y-m-d H:i:s",$time); //2010-08-29;
        //获取前台数据
        $userData['username']=$this->getRequest()->getParam("username","");
        $userData['password']=$this->getRequest()->getParam("password","");
        $userData['showname']=$this->getRequest()->getParam("showname","");
        $userData['cellphone']=$this->getRequest()->getParam("cellphone","");
        $userData['email']=$this->getRequest()->getParam("email","");
        $userData['wechart']=$this->getRequest()->getParam("wechart","");
        $userData['time_reg']=$time;
       
        //$validCode=$this->getRequest()->getParam("validCode","");//验证码
       // file_put_contents("c:/user_id.log", "userid:".$user_id." validCode:".$validCode."\r\n",FILE_APPEND);
        $info="";
        $arr=array();
        $arr[0]['username']="用户 OK";
        $arr[0]['email']="Email OK";
        $arr[0]['cellphone']="cellphone OK";
        //$arr[0]['vali']="验证码 OK";
        //判断用户名是否注册过
           $user=new users();
           if($user->checkusername($userData['username']))//如果存在
           {
               $arr[0]['username']="用户已存在！";
           //    file_put_contents("c:/ajax.log", $arr['a']."\r\n",FILE_APPEND);
           }
        //判断邮箱是否注册过
           if($user->checkemail($userData['email'])&&$userData['email']!='')//如果存在
           {
               $arr[0]['email']="email已存在！";
           }
           //判断手机是否注册过
           if($user->checkcellphone($userData['cellphone'])&&$userData['cellphone']!='')//如果存在
           {
               $arr[0]['cellphone']="cellphone已存在！";
           }
        //验证session
        /*
        $codeSession = new Zend_Session_Namespace('captcha_code_'.$user_id);
        file_put_contents("c:/user_id.log", " validCode:".$codeSession->code."\r\n",FILE_APPEND);
        if ($codeSession != null && strtolower($codeSession->code) == strtolower($validCode)) {
            //验证码通过，入录数据库
            $arr[0]['vali']="验证码 OK";
        }
        else{
            $arr[0]['vali']="验证码错误";
        }
        */
           if($arr[0]['username']=="用户 OK"&&$arr[0]['email']=="Email OK"&&$arr[0]['cellphone']=="cellphone OK")
        {
            //入录数据库
            $users=new users();
            if($users->createUser($userData))
            {
               //创建成功后。。。
            }
        }
        $info=json_encode($arr);
        $this->view->info=$info;
        $this->render('ajax');
    }
    
    public function showusersbydepAction()
    {
        $dep=$this->getRequest()->getParam("dep","");//
        $users=new users();
        $res=$users->getUsersByDep($dep);
        $info=json_encode($res);
        $this->view->info=$info;
        $this->render('ajax'); 
    }
    //pwd修改
    public function changepwdAction()
    {
        $pwd=$this->getRequest()->getParam("pwd","");//
        $uid=$this->getRequest()->getParam("uid","");//
        $res=0;
        if($pwd!=''&&$uid!='')
        {
            $users=new users();
            $res=$users->update_pwd($uid, $pwd);
        }
        $this->view->info=$res;
        $this->render('ajax');
    }
    //pwd重置
    public function resetpwdAction()
    {
        $uid=$this->getRequest()->getParam("uid","");//
        $res=0;
        if($uid!='')
        {
            $users=new users();
            $res=$users->update_pwd($uid, 1);
        }
        $this->view->info=$res;
        $this->render('ajax');
    }
    //email修改
    public function changeemailAction()
    {
        $email=$this->getRequest()->getParam("email","");//
        $uid=$this->getRequest()->getParam("uid","");//
        $res=0;
        if(filter_var($email, FILTER_VALIDATE_EMAIL)&&$uid!='')
        {
            $users=new users();
            $res=$users->update_email($uid, $email);
        }
        $this->view->info=$res;
        $this->render('ajax');
    }
    //手机号码修改
    public function changecellphoneAction()
    {
        $cellphone=$this->getRequest()->getParam("cellphone","");//
        $uid=$this->getRequest()->getParam("uid","");//
        $res=0;
        if(preg_match("/^1[34578]{1}\d{9}$/",$cellphone)&&$uid!='')
        {
            $users=new users();
            $res=$users->update_cellphone($uid, $cellphone);
        }
        $this->view->info=$res;
        $this->render('ajax');
    }
    //短信通知
    public function notificationAction()
    {
        $notification=$this->getRequest()->getParam("notification","");//
        $uid=$this->getRequest()->getParam("uid","");//
        $res=0;
            $users=new users();
            $res=$users->update_notification($uid, $notification);
        $this->view->info=$res;
        $this->render('ajax');
    }
    //用户信息界面
    public function userinfoAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
        
            $this->redirect('/index/index');
            exit();
        }
        $userid=$this->getRequest()->getParam("userid","");//userid
        $user=new users();
        $info=$user->getUserbyId($userid);
        $nodeuser=new nodeusers();
        $nodeuserinfo=$nodeuser->getUser($userid);
        $this->view->loginname=$_SESSION['loginuser'];
        $this->view->info=$info;
        $code=$this->getRequest()->getParam("code","");//code
        $this->view->code=$code;
        $this->view->nodeinfo=$nodeuserinfo;
        $t1=new t1();
        $this->view->totaldata=$t1->getdata();
		$t2=new t2();
        $row=$t2->getpname();
        $this->view->pname=$row;
		$this->render('user');
    }
	
	public function usernodeAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
        
            $this->redirect('/index/index');
            exit();
        }
        $userid=$this->getRequest()->getParam("uid","");//userid
        $code=$this->getRequest()->getParam("code","");//code
        $user=new users();
        $info=$user->getUserbyId($userid);
        $nodeuser=new nodeusers();
        $nodeuserinfo=$nodeuser->getUser($userid);
        if($code!="")
        {$nodeuserinfo=$nodeuser->getUserbyCode($userid,$code);}
        
        $this->view->info=$info;
        $this->view->nodeinfo=$nodeuserinfo;
        $t1=new t1();
        $this->view->totaldata=$t1->getdatabycode($code);
      
        $this->render('usernode');
    } 
	


    //选择成员
    public function chosememberAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $code=$this->getRequest()->getParam("code","");//code
        $node=$this->getRequest()->getParam("node","");//node
        //获取节点信息
        $t1=new t1();
        $nodeinfo=$t1->getdata_by_node($node, $code);
        //获取所有用户
        $users=new users();
        $allusers=$users->getUsers();
        //获取节点成员
        $nodeusers=new nodeusers();
        $members=$nodeusers->getMemberByProjectNode($code, $node);
        
        $this->view->loginname=$_SESSION['loginuser'];
        $this->view->members=$members;
        $this->view->count=count($members);
        $this->view->code=$code;
        $this->view->node=$node;
        $this->view->allusers=$allusers;
        $this->view->nodeinfo=$nodeinfo;
        $this->render('chosemember');
    }
    //选择成员和主管
    public function chosemanagerAction()
    {
    	if (!session_id())session_start();
    	if (!isset($_SESSION['loginuser']))
    	{
    
    		$this->redirect('/index/index');
    		exit();
    	}
    	
    	$code=$this->getRequest()->getParam("code","");//code
    	$node=$this->getRequest()->getParam("node","");//node
    	//获取节点信息
    	$t1=new t1();
    	$nodeinfo=$t1->getdata_by_node($node, $code);
    	//获取所有用户
    	$users=new users();
    	$allusers=$users->getUsers();
    	//获取节点成员
    	$nodeusers=new nodeusers();
    	$members=$nodeusers->getMemberByProjectNode($code, $node);
    
    	$this->view->loginname=$_SESSION['loginuser'];
    	$this->view->members=$members;
    	$this->view->count=count($members);
    	$this->view->code=$code;
    	$this->view->node=$node;
    	$this->view->allusers=$allusers;
    	$this->view->nodeinfo=$nodeinfo;
    	$this->render('chosemanager');
    }
    //显示成员
    public function showmemberAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $code=$this->getRequest()->getParam("code","");//code
        $node=$this->getRequest()->getParam("node","");//node
        //获取所有用户
        $users=new users();
        $allusers=$users->getUsers();
        //获取节点成员
        $nodeusers=new nodeusers();
        $members=$nodeusers->getMemberByProjectNode($code, $node);
    
        $this->view->loginname=$_SESSION['loginuser'];
        $this->view->members=$members;
        $this->view->count=count($members);
        $this->view->code=$code;
        $this->view->node=$node;
        $this->view->allusers=$allusers;

        $this->render('ajaxmembers');
    }
    

    //add成员
    public function addmemberAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $info=null;
        $code=$this->getRequest()->getParam("code","");//code
        $node=$this->getRequest()->getParam("node","");//node
        $userid=$this->getRequest()->getParam("userid","");//userid
        $username=$this->getRequest()->getParam("username","");//username
        $position=$this->getRequest()->getParam("position","0");//username;
        //获取所有用户
        $users=new users();
        $allusers=$users->getUsers();
        $userid=$users->getUseridbycode($userid);
        //判断是否存在
        $nodeusers=new nodeusers();
        $isexist=$nodeusers->isMemberByProjectNode($code, $node, $userid);
        if(!$isexist)
        {
            //add
            $add=$nodeusers->add($code, $node, $userid, $username, $position);
            if($add>0)
            {
                $info=1;
            }
            else 
            {$info=0;}
        }
    
        $this->view->loginname=$_SESSION['loginuser'];
        $this->view->code=$code;
        $this->view->node=$node;
        $this->view->info=$info;
    
        $this->render('ajax');
    }
    
    //del成员
    public function delmemberAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $info=null;
        $code=$this->getRequest()->getParam("code","");//code
        $node=$this->getRequest()->getParam("node","");//node
        $userid=$this->getRequest()->getParam("id","");//userid
        
        //获取所有用户
        $users=new users();
        $allusers=$users->getUsers();
        //判断是否存在
        $nodeusers=new nodeusers();
        $isexist=$nodeusers->isMemberByProjectNode($code, $node, $userid);
        if($isexist)
        {
            //del
            $del=$nodeusers->del($code, $node, $userid);
            if($del>0)
            {
                $info=1;
            }
            else
            {$info=0;}
        }
    
        $this->view->loginname=$_SESSION['loginuser'];
        $this->view->code=$code;
        $this->view->node=$node;
        $this->view->info=$info;
    
        $this->render('ajax');
    }


	    //改变项目类别
    public function changetypeAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $info="<option></option>";
        $type=$this->getRequest()->getParam("type","");//type
                file_put_contents("c:/type.log", $type."\r\n",FILE_APPEND);
        $t2=new t2();
        $row=$t2->getpname_type($type);
		if(count($row)>0)
		for($i=0;$i<count($row);$i++)
		{
			$info.="<option>". $row[$i]['项目']."</option>";
		}
        $this->view->info=$info;
    
        $this->render('ajax');
    }
}

