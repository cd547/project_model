<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/t3.php';//model table
require_once APPLICATION_PATH.'/models/t1.php';//model table
require_once APPLICATION_PATH.'/models/t2.php';//model tables
require_once APPLICATION_PATH.'/models/upfile.php';//model tables
require_once APPLICATION_PATH.'/models/mylog.php';//model tables
require_once APPLICATION_PATH.'/models/users.php';//model tables
require_once APPLICATION_PATH.'/models/mail.php';//model table
require_once APPLICATION_PATH.'/models/message.php';//model table

/*控制器用于响应登录推出*/
class MylogController extends BaseController
{
	
    //工作日志界面
    public function mylogAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        
       // $this->view->res=$arr1;
      //   $this->view->today=$today;
        $time = time();
        date_default_timezone_set('PRC'); //设置中国时区
        $creatdate= date("y-m-d H:i:s",$time);
        $this->view->loginname=$_SESSION['loginuser'];
        $users=new users();
        $res=$users->getUser($_SESSION['loginuser']['cellphone']);
        $mylog=new mylog();
        $manager="0";
        $mymanager=null;
        $dep=$res[0]['dep'];
        if($mylog->isnotmanager($_SESSION['loginuser']['id']))
        {
            $manager="1";
        }
        else 
        {
            $mymanager=$mylog->getmanager($dep);
        }
        $this->view->manager=$manager;
        $this->view->mymanager=$mymanager;
        $this->view->dep=$dep;
        $this->view->creatdate=$creatdate;
        $this->render();
    }
    
    public function myloglistAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $mylog=new mylog();
        $user=new users();
        $cellphone=$user->getUserPhone($_SESSION['loginuser']['id']);
        $res=$mylog->findsave($cellphone);
        $this->view->myloglist=$res;
        $this->view->loginname=$_SESSION['loginuser'];
        $this->render();
    }
    //显示查询列表
    public function loglistAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $mylog=new mylog();
        $user=new users();
        $usercode=$user->getUserbyId($_SESSION['loginuser']['id'])[0]['code'];
        $search=$this->getRequest()->getParam("search","");//
      //  file_put_contents("c:/search.log", $search."\r\n",FILE_APPEND);
        $res=$mylog->findpost($usercode,$search);
        $paginator = Zend_Paginator::factory($res);
        $paginator->setItemCountPerPage(10);//每页行数
        $paginator->setPageRange(10);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->requestParams=array('search'=>$search);
        $this->view->paginator = $paginator;
        $this->view->loginname=$_SESSION['loginuser'];
        $this->view->search=$search;
        $this->render();
    }
    
    //显示已完成列表
    public function loghistoryAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $search="";
        $mylog=new mylog();
        $user=new users();
        $usercellphone=$user->getUserbyId($_SESSION['loginuser']['id'])[0]['cellphone'];
        
        $res=$mylog->findmylog($usercellphone);
        $paginator = Zend_Paginator::factory($res);
        $paginator->setItemCountPerPage(10);//每页行数
        $paginator->setPageRange(10);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->requestParams=array('search'=>$search);
        $this->view->paginator = $paginator;
        $this->view->loginname=$_SESSION['loginuser'];
        $this->render();
    }
    //项目问题解决界面
    public function editAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $id=$this->getRequest()->getParam("id","");//id
        $users=new users();
        $res=$users->getUser($_SESSION['loginuser']['cellphone']);
        $mylog=new mylog();
        $manager="0";
        $mymanager=null;
        $dep=$res[0]['dep'];
        if($mylog->isnotmanager($_SESSION['loginuser']['id']))
        {
            $manager="1";
        }
        else 
        {
            $mymanager=$mylog->getmanager($dep);
        }
        $this->view->manager=$manager;
     
        $this->view->mymanager=$mymanager;
        $res=$mylog->findmylog_id($id);
        $this->view->res=$res;
        $this->view->cellphone= $users->getUserbycode($res[0]['manager'])[0]['cellphone'];
        $this->view->id=$id;
        $this->view->loginname=$_SESSION['loginuser'];
        $this->view->dep=$dep;
        $this->render('edit');
    }
    
    public function getmanagersbydepAction()
    {
        $dep=$this->getRequest()->getParam("dep","");//
        $mylog=new mylog();
        $res=$mylog->getmanagersByDep($dep);
        $info=json_encode($res);
        $this->view->info=$info;
        $this->render('ajax');
    }
    
    public function showdepsAction()
    {
        $dep=$this->getRequest()->getParam("dep","");//
        $mylog=new mylog();
        $res=$mylog->showdeps();
        $info=json_encode($res);
        $this->view->info=$info;
        $this->render('ajax');
    }
    
    //提交
    public function mylogpostAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $time = time();
        date_default_timezone_set('PRC'); //设置中国时区
        $dep=$this->getRequest()->getParam("report_dep","");
        $username=$this->getRequest()->getParam("reporter","");
		$userid=$this->getRequest()->getParam("reporterid","");//cellphone!!!!!!!!!!!
        $content=$this->getRequest()->getParam("report_content","");
        $title=$this->_request->getPost('title');
        $report_content=stripslashes($content);//该函数可用于清理从数据库中或者从 HTML 表单中取回的数据。
        $helpercellphone=$this->getRequest()->getParam("helper","");//审阅对象手机号
        file_put_contents("c:/cellphone.log",$helpercellphone."\r\n",FILE_APPEND);
        $saveorpost=$this->_request->getPost('saveorpost');//save:0;post:1
        $date= date("y-m-d H:i:s",$time);
        $mylog=new mylog();
        $num=null;
        $user=new users();
        $manager=$user->getUser($helpercellphone)[0]['code'];
        file_put_contents("c:/cellphone.log",$manager."\r\n",FILE_APPEND);
        $managername=$user->getUserbycode($manager)[0]['username'];
        if($saveorpost=="0")
        {
            $num=$mylog->savemylog($username, $userid, $title, $dep, $content, $manager,$managername, $date);
        }
        else if($saveorpost=="1")
        {
            $num=$mylog->postmylog($username, $userid, $title, $dep, $content, $manager,$managername, $date);
        }
        else if($saveorpost=="2")//更新
        {
            $id=$this->_request->getPost('id');
            $num=$mylog->updatemylog($id,$username, $userid, $title, $dep, $content, $manager,$managername, $date);
        }
        else if($saveorpost=="3")//更新
        {
            $id=$this->_request->getPost('id');
            $num=$mylog->updatepostmylog($id,$username, $userid, $title, $dep, $content, $manager,$managername, $date);
        }
        $this->view->info=$num;
        $this->render('ajax');
    }
    //删除
    public function delAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $id=$this->getRequest()->getParam("id","");
        $mylog=new mylog();
        $num=$mylog->del($id);
            $this->redirect('/mylog/myloglist');
    }
    //快速查阅
    public function quickckAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $time = time();
        date_default_timezone_set('PRC'); //设置中国时区
        $id=$this->getRequest()->getParam("id","");
        $mylog=new mylog();
        $date= date("y-m-d H:i:s",$time);
        $num=$mylog->checklog($id,$date);
        $this->redirect('/mylog/loglist');
    }
    //显示查阅页面
    public function showcheckAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $id=$this->getRequest()->getParam("id","");//id
        $mylog=new mylog();
        $res=$mylog->findmylog_id($id);
        $this->view->res=$res;
        $this->view->id=$id;
        $this->view->loginname=$_SESSION['loginuser'];
        $this->render();
    }
    
    //显示已经查阅页面
    public function showcheckedAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $id=$this->getRequest()->getParam("id","");//id
        $mylog=new mylog();
        $res=$mylog->findmylog_id($id);
        $this->view->res=$res;
        $this->view->id=$id;
        $this->view->loginname=$_SESSION['loginuser'];
        $this->render();
    }
    
    
    public function checkAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $time = time();
        date_default_timezone_set('PRC'); //设置中国时区
        $id=$this->getRequest()->getParam("id","");
        $mylog=new mylog();
        $date= date("y-m-d H:i:s",$time);
        $num=$mylog->checklog($id,$date);
        $this->view->info=$num;
        $this->render('ajax');
    }
    
    //解决方案提交
    public function handlepostAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $time = time();
        date_default_timezone_set('PRC'); //设置中国时区
        $handle_content=$this->getRequest()->getParam("handle_content","");
        $id=$this->getRequest()->getParam("id","");
        $handle_title=$this->getRequest()->getParam("handle_title","");
		$quester=$this->getRequest()->getParam("quester","");
		$users=new users();
		$questercellphone=$users->getcellphonebyname($quester);
        $date= date("y-m-d H:i:s",$time);
        $problem=new problem();
        $num=$problem->handleproblem_id($id, $handle_title,$handle_content, $_SESSION['loginuser']['username'], $date);

                $message=new message();
				$msg_content="尊敬的".$quester."您好！您之前发送给".$_SESSION['loginuser']['username']."的问题报告，对方已经回复，请到相关节点明细中的重大问题报告标签查阅，谢谢！";
                $msg=$message->sendv2($questercellphone,$msg_content);

        $this->view->info=$num;
        $this->render('ajax');
    }
    
   //ajax查询
    public function searchreportAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
        
            $this->redirect('/index/index');
            exit();
        }
        date_default_timezone_set('PRC');
        if (!session_id())session_start();
        $node=$this->getRequest()->getParam("node","");//node
        $code=$this->getRequest()->getParam("project","");//p
        $problem=new problem();
        $filecontext=$problem->findproblem_code_node($code, $node);
        $this->view->filecontext=$filecontext;
		$this->view->code=$code;
		$this->view->node=$node;
         $this->view->user=$_SESSION['loginuser']['cellphone'];
        $this->render('detajax1');
    }
    
    //显示问题
    public function showproblemsAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $time = time();
        date_default_timezone_set('PRC'); //设置中国时区
        $project=$this->getRequest()->getParam("project","");
        $user=$this->getRequest()->getParam("user","");
        $problem=new problem();
        $problems=$problem->searchproblemsnum_helper_project($user, $project);
        $arr=array();
      
        for($i=0;$i<count($problems);$i++)
        {
            //$arr[$i]['id']=$i;
            $arr[$i]['node']=$problems[$i]['节点'];
            $arr[$i]['num']=$problems[$i]['num'];
        }
        $info=json_encode($arr);
        $this->view->info=$info;
        $this->render('ajax');
    }
    //显示问题报告
    public function showproblemAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $time = time();
        date_default_timezone_set('PRC'); //设置中国时区
       $id=$this->getRequest()->getParam("id","");//id
        $problem=new problem();
        $res=$problem->findproblem_id($id);
        $this->view->res=$res;
        $this->view->loginname=$_SESSION['loginuser'];
        $this->render('showproblem');
    }
}

