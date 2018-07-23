<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/users.php';//model table
require_once APPLICATION_PATH.'/models/pro_level1.php';//pro_level1表
require_once APPLICATION_PATH.'/models/pro_analysis.php';//pro_analysis表
require_once APPLICATION_PATH.'/models/pro_count.php';//pro_count表

/*控制器用于响应登录*/
class ProjectController extends BaseController
{
    //项目主页
	public function prolevel1Action()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $pro_num=$this->getRequest()->getParam("pro_num","");
        //获取项目信息
        $pro_level1=new pro_level1();
        $db=$pro_level1->getAdapter();
        $sql=$db->select()->from('pro_level1','*')->where('pro_num=?',$pro_num);
        $result= $db->query($sql);
        $rows=$result->fetchAll();
        //项目信息
        $this->view->project_data=$rows;
        //获取pro_analysis数据
        $pro_analysis=new pro_analysis();
        $db=$pro_analysis->getAdapter();
        $sql=$db->select()->from('pro_analysis','*')->where('pro_num =?',$pro_num)->order('id ASC');
        $paginator = Zend_Paginator::factory($sql);
        $paginator->setItemCountPerPage(5);//每页行数
        $paginator->setPageRange(10);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;
        $this->view->requestParams=$pro_num;
        //用户信息
        $this->view->loginuser=$_SESSION['loginuser'];
        $this->render('prolevel1');
    }

//跳转添加项目页面
    public function goaddlv1Action()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        //用户信息
        $this->view->loginuser=$_SESSION['loginuser'];
        $this->render('addlv1');
    }
    //ajax项目分析分页
    public function ajaxpageAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        //获取前台查询数据和页码
        $page=$this->getRequest()->getParam("page","1");
        $query=$this->getRequest()->getParam("query","");
        $pro_analysis=new pro_analysis();
        $db=$pro_analysis->getAdapter();
        $sql=$db->select()->from('pro_analysis','*')->where('pro_num =?',$query)->order('id ASC');

        $paginator = Zend_Paginator::factory($sql);
        $paginator->setItemCountPerPage(5);//每页行数
        $paginator->setPageRange(10);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;
        $this->view->requestParams=$query;
        $this->view->page=$page;
        //用户信息
        $this->view->loginuser=$_SESSION['loginuser'];
        $this->render('ajaxpage');
    }

    public function ajaxaddAction()
    {
        if (!session_id()) session_start();
        if (!isset($_SESSION['loginuser'])) {
            $this->redirect('/index/index');
            exit();
        }
        $adduser=$_SESSION['loginuser'];

        //获取前台数据
        $getdata=$this->getRequest()->getParams();
        //file_put_contents("../log/useradd.log", $adduser['username']."\r\n",FILE_APPEND);
        $returnans=array();
        $validate=true;
        {
            //验证
            if(trim($getdata['pro_endMoney'])=='')
            { $getdata['pro_endMoney']=0;}
            if(trim($getdata['pro_reportMoney'])=='')
            { $getdata['pro_reportMoney']=0;}
            if(trim($getdata['pro_name'])==''||trim($getdata['pro_startTime'])==''||trim($getdata['pro_endTime'])=='')
            {
                $validate=false;
            }
        }
        if($validate)
        {
            //计数+1
            $pro_count=new pro_count();
            $pro_num=$pro_count->create_project_count();
            /*
            * 补0:0099
            *strlen()
            */
            while(strlen($pro_num)<4)
            {
                $pro_num='0'.$pro_num;
            }

            //数组整理
            date_default_timezone_set('PRC'); //设置中国时区
            $time = time();
            $this_year = date("Y",$time);
            $a=array(
                'pro_enterPeople'=>$adduser['username'],
                'pro_num'=>'xm'.$this_year.$pro_num
            );
            $data=array_merge($getdata,$a);
            $data=array_diff($data, [$data['controller'], $data['action'],$data['module']]);
           // file_put_contents("../log/useradd.log", ."\r\n",FILE_APPEND);

            $pro_level1=new pro_level1();
            $rowid=$pro_level1->create_project($data);
            $returnans['id']=$rowid;
            $returnans['pro_name']=$data['pro_name'];
            $returnans['pro_num']=$data['pro_num'];
            $returnans['val']=true;
        }
        else{
            $valtext="";
            if(trim($getdata['pro_name'])=='')
            {
                $valtext.="项目名称不能为空！<br>";
            }
            if(trim($getdata['pro_startTime'])=='')
            {
                $valtext.="项目开始时间不能为空！<br>";
            }
            if(trim($getdata['pro_endTime'])=='')
            {
                $valtext.="项目结束时间不能为空！<br>";
            }

            //验证失败
            $returnans['val']=$valtext;
        }
        $this->view->info= json_encode($returnans);
        $this->render('ajax');
    }

    //ajax 删除项目
    public function ajaxdeleteproAction()
    {
        if (!session_id()) session_start();
        if (!isset($_SESSION['loginuser'])) {
            $this->redirect('/index/index');
            exit();
        }
        $pro_num=$this->getRequest()->getParam("pro_num","");
        $pro_level1=new pro_level1();
        $rows_affected =$pro_level1->deletepro($pro_num);

        //日志输出
       // file_put_contents("../log/db.log", $rows_affected."\r\n",FILE_APPEND);
        $this->view->info=json_encode($rows_affected);
        $this->render('ajax');
    }

    public  function  ajaxlv1Action()
    {
        if (!session_id()) session_start();
        if (!isset($_SESSION['loginuser'])) {
            $this->redirect('/index/index');
            exit();
        }
        $pro_num=$this->getRequest()->getParam("pro_num","");
        //获取项目信息
        $pro_level1=new pro_level1();
        $db=$pro_level1->getAdapter();
        $sql=$db->select()->from('pro_level1','*')->where('pro_num=?',$pro_num);
        $result= $db->query($sql);
        $rows=$result->fetchAll();
        $this->view->info= json_encode($rows);
        $this->render('ajax');
    }

}

