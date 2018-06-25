<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/users.php';
require_once APPLICATION_PATH.'/models/mail.php';
class UserController extends BaseController
{
    //用户信息界面
    public function indexAction()
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
        //用户信息
        $this->view->loginuser=$_SESSION['loginuser'];
        $this->render();
    }
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
        $user_id=$this->getRequest()->getParam("userid","");
        $userData['time_reg']=$time;
        $validCode=$this->getRequest()->getParam("validCode","");//输入的验证码
       // file_put_contents("c:/user_id.log", "userid:".$user_id." validCode:".$validCode."\r\n",FILE_APPEND);
        $info="";
        $arr=array();
        $arr[0]['username']="用户 OK";
        $arr[0]['email']="邮箱 OK";
        $arr[0]['cellphone']="手机 OK";
        $arr[0]['code']="验证码 OK";
        //判断用户名是否注册过
           $user=new users();
           if($user->checkusername($userData['username']))//如果存在
           {
               $arr[0]['username']="用户已存在！";
           }
        //判断邮箱是否注册过
           if($user->checkemail($userData['email'])&&$userData['email']!='')//如果存在
           {
               $arr[0]['email']="邮箱已存在！";
           }
           //判断手机是否注册过
           if($user->checkcellphone($userData['cellphone'])&&$userData['cellphone']!='')//如果存在
           {
               $arr[0]['cellphone']="手机已存在！";
           }
        //验证session
        $codeSession = new Zend_Session_Namespace('captcha_code_'.$user_id);
        file_put_contents("../log/user_id.log", " validCode:".$codeSession->code."\r\n",FILE_APPEND);
        if ($codeSession != null && strtolower($codeSession->code) == strtolower($validCode)) {
            //验证码通过，入录数据库
            $arr[0]['code']="验证码 OK";
        }
        else{
            $arr[0]['code']="验证码错误";
        }

           if($arr[0]['username']=="用户 OK"&&$arr[0]['email']=="邮箱 OK"&&$arr[0]['cellphone']=="手机 OK"&&$arr[0]['code']=="验证码 OK")
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
    //显示名修改
    public function changeshownameAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $showname=$this->getRequest()->getParam("showname","");//
        $username=$this->getRequest()->getParam("username","");//
        $res=0;
        if($showname!=''&&$username!='')
        {
            $users=new users();
            $res=$users->update_showname($username, $showname);
            if($res==1)//更新成功
            {
                //session更新
                $loginuser=$users->getUserbyusername($username);
                $_SESSION['loginuser']=$loginuser[0];
            }
        }
        $this->view->info=$res;
        $this->render('ajax');
    }
    //pwd修改
    public function changepwdAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $pwd=$this->getRequest()->getParam("pwd","");//
        $username=$this->getRequest()->getParam("username","");//
        $res=0;
        if($pwd!=''&&$username!='')
        {
            $users=new users();
            $res=$users->update_pwd($username, $pwd);
            if($res==1)//更新成功
            {
                //session更新
                $loginuser=$users->getUserbyusername($username);
                $_SESSION['loginuser']=$loginuser[0];
            }

        }
        $this->view->info=$res;
        $this->render('ajax');
    }
    //pwd重置
    public function resetpwdAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {

            $this->redirect('/index/index');
            exit();
        }
        $username=$this->getRequest()->getParam("username","");//
        $res=0;
        if($username!='')
        {
            $users=new users();
            $res=$users->update_pwd($username, 1);
            if($res==1)//更新成功
            {
                //session更新
                $loginuser=$users->getUserbyusername($username);
                $_SESSION['loginuser']=$loginuser[0];
            }
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

}

