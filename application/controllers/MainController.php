<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/users.php';//model table

/*控制器用于响应登录推出*/
class MainController extends BaseController
{
    //项目大厅入口
	public function indexAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $this->view->loginuser=$_SESSION['loginuser'];
        $this->render();
          }

//跳转主页
    public function mainAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }

    }

    //ajax显示模态框
    public function showdetAction()
    {
        date_default_timezone_set('PRC');
        if (!session_id())session_start();
        $node=$this->getRequest()->getParam("node","");//node
        $code=$this->getRequest()->getParam("code","");//node
        //file_put_contents("c:/c.log",$node."\r\n",FILE_APPEND);
        $t1=new t1();
        $data=array();
        $data=$t1->getdata_by_node($node,$code);
        //file_put_contents("c:/c.log",$data[0]['节点名称']."\r\n",FILE_APPEND);
        $state=$data[0]['状态'];
        $state_num=$state;
        $isfinished=$state;
        $statecolor="#ffffff";
        $row=$t1->getdata_by_node($node,$code);
        if($state==0)
        {
           $state="default";
          
           $start=$row[0]['开始识别'];
           if($start==1)//可以提前开始
           {
               $statecolor="#A757A8";
           }
           elseif($start==2)//将延迟开始
           {
               $statecolor="#FFD39B";
           }
           else
           {
               $statecolor="gray";
           }
        }
        elseif($state==1)
        {
            $state="success";
        }
        elseif($state==2)
        {
            $state="primary";
            $end=$row[0]['结束识别'];
            if($end==1)//流程已经延迟结束
            {
                $statecolor="#FF6347";
            }   
        }
        $this->view->node=$node;
        $this->view->code=$code;
        $this->view->state=$state;
        $this->view->statecolor=$statecolor;
		$befor_nodes = explode(',',$data[0]['前置任务']);//获取当前流程前置任务
		$befor_nodes_s=array();
		if($befor_nodes[0]!=0)
		{
		  $num=count($befor_nodes);
		  $befor_state=array();
		//判断任务完成情况
		  for($i=0;$i<$num;$i++)
		  {
			$befor_state[$i]=$t1->getstate($befor_nodes[$i],$code);
		  }

		     $canstart=1;//1:ok,0:no

		  for($i=0;$i<$num;$i++)
		  {
			$befor_nodes_s[$i]['node']=$befor_nodes[$i];
			if($befor_state[$i]==0)
			{
				$befor_nodes_s[$i]['state']="default";
				$canstart=$canstart*0;
			}
			elseif($befor_state[$i]==1)
			{
				$befor_nodes_s[$i]['state']="success";
				$canstart=$canstart*0;
			}
			elseif($befor_state[$i]==2)
			{
				$befor_nodes_s[$i]['state']="primary";
			}
		  }
		}
		else 
		{
		    $befor_nodes_s[0]['node']=0;
		    $befor_nodes_s[0]['state']="success";
		    $canstart=1;
		}
		//
		if($isfinished==2)
		{ $canstart=0;}
		
		$upfile=new upfile();
		$filecontext=$upfile->findfile_bynode($code, $node);
		$this->view->filecontext=$filecontext;
		$t2=new t2();
		$today=$t2->gettoday($code);
		$this->view->today=$today;
        $this->view->nodename=$data[0]['节点名称'];
        $this->view->p_task=$befor_nodes_s;
        $this->view->state_num=$state_num;//节点状态
        $this->view->p_startdate=$data[0]['启始日期'];
        $this->view->P_enddate=$data[0]['终止日期'];
        $this->view->startdate=$data[0]['实际开始日期'];
        $this->view->enddate=$data[0]['实际结束日期'];
		$this->view->needdays=$data[0]['需要天数'];
        $this->view->canstart=$canstart;
        $this->view->loginuser=$_SESSION['loginuser'];
		$res=$t1->getdata_by_node($node,$code);
		$user=new users();

		$dep1=$user->get_dep($res[0]['user1']);
		 $this->view->user1=$dep1.":".$res[0]['user1'];
		 $dep2=$user->get_dep($res[0]['user2']);
		 $this->view->user2=$dep2.":".$res[0]['user2'];
		$nodeuser=new nodeusers();
		$this->view->ismember=$nodeuser->isnodemember($_SESSION['loginuser']['username'],$code,$node);
		$ischange=0;
		if($_SESSION['loginuser']['position']=='董事长')
		{
			$res=$t1->getdata_by_node($node,$code);
			if($res[0]['c4']==1){;}
			else{$ischange=1;}
		}
		elseif($_SESSION['loginuser']['position']=='总经理')
		{
			$res=$t1->getdata_by_node($node,$code);
			if($res[0]['c4']==1){;}
			else{
				if($res[0]['c3']==1){;}
				else
				{$ischange=1;}
				}
		}
		elseif($_SESSION['loginuser']['position']=='副总经理')
		{
		$res=$t1->getdata_by_node($node,$code);
			if($res[0]['c4']==1){;}
			else{
					if($res[0]['c3']==1){;}
					else
					{
						if($res[0]['c2']==1){;}
						else{$ischange=1;}
					}
				}
		}
		elseif($_SESSION['loginuser']['position']=='经理')
		{
			$res=$t1->getdata_by_node($node,$code);
			if($res[0]['c4']==1){;}
			else{
					if($res[0]['c3']==1){;}
					else
					{
						if($res[0]['c2']==1){;}
						else{
							if($res[0]['c1']==1){;}
							else{$ischange=1;}
							}
					}
				}
		}
		 $this->view->ischange=$ischange;
		 $this->view->type=$t2->gettype($code);
        $this->render('detajax1');
    }

}

