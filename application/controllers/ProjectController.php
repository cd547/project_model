<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/users.php';//model table
require_once APPLICATION_PATH.'/models/pro_level1.php';//pro_level1表
require_once APPLICATION_PATH.'/models/pro_analysis.php';//pro_analysis表

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
        file_put_contents("../log/db.log", $rows_affected."\r\n",FILE_APPEND);
        $this->view->info=$rows_affected;
        $this->render('ajax');
    }
   

}

