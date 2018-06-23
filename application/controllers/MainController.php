<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/users.php';//model table
require_once APPLICATION_PATH.'/models/t3.php';//model table
require_once APPLICATION_PATH.'/models/t1.php';//model table
require_once APPLICATION_PATH.'/models/t2.php';//model tables
require_once APPLICATION_PATH.'/models/upfile.php';//model tables
require_once APPLICATION_PATH.'/models/projectdate.php';//model tables
require_once APPLICATION_PATH.'/models/nodeusers.php';//model tables
require_once APPLICATION_PATH.'/models/change_history.php';//model tables

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
//工作台入口
    public function myworkAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
            $this->redirect('/index/index');
            exit();
        }
        $nodeusers=new nodeusers();
        $res=$nodeusers->getprojectsbyuser($_SESSION['loginuser']['id']);
        $this->view->loginname=$_SESSION['loginuser'];
        $this->view->res=$res;
        $this->render('mywork');
    }
    
	public function settimeAction()
	{
		$today=$this->getRequest()->getParam("data","");//标题
		$code=$this->getRequest()->getParam("code","");//
		//file_put_contents("c:/ct.log",$today."\r\n",FILE_APPEND);
		if($code)
		{
	
		  $t2=new t2();
		  $time=$t2->settoday($today,$code);
		  date_default_timezone_set('PRC'); //设置中国时区
		  $time = time();
		  $t2=new t2();
		  $t2->setupdatetime($code, date("Y-m-d H:i:s",$time));
		}
		//$this->redirect('/main/main');
		$this->render('ajax');
	}
	//获取t2表时间
	public function gettimeAction()
	{
	    if (!session_id())session_start();
	    if (!isset($_SESSION['loginuser']))
	    {
	        $this->redirect('/index/index');
	        exit();
	     }
	     $code=$this->getRequest()->getParam("code","陈家镇第二卫生服务中心");//code
	        $t2=new t2();
	        $time=$t2->gettoday($code);

	   $this->view->info=$time;
	    $this->render('ajax');
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
       // $redis=new Redis();
       // $redis->connect('127.0.0.1',6379);
       // $redis->SET(session_id(), $_SESSION['loginuser']['username']);
        $code=$this->getRequest()->getParam("code","陈家镇第二卫生服务中心");//code

        $t1=new t1();
        $nodecount=$t1->getnodecount($code);
		$t2=new t2();
		//判断项目是否存在
		if($t2->getproject($code)==0)
		{
		    $this->render();
		}
		else {
		$time=$t2->gettoday($code);
		 //$time=$this->getRequest()->getParam("data","");//标题
		file_put_contents("c:/c.log",$time."\r\n",FILE_APPEND);
        for($i=1;$i<=$nodecount;$i++)
        {
            //$this->checkstate($i,$time);
            $this->checkstate1($i,$time,$code);
        }
        $type=$t2->gettype($code);
		//$t3=new t3();
		//$sql="SELECT * FROM t1 LEFT JOIN t3 on t1.提示 =t3.提示 ORDER BY t1.id ASC;";
		$sql="SELECT * FROM t1 WHERE 项目='".$code."' ORDER BY 流程 ASC;";
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$result = $db->query($sql);
    	$data =$result->fetchAll();
		$rownum=$result->rowCount();
		$color=array();
		$bef=array();
		$aft=array();
		$nodecontent=array();
		for($i=0;$i<$rownum;$i++)
		{
		    $nodecontent[$i]['节点名称']=$data[$i]['节点名称'];
			$nodecontent[$i]['实际开始日期']=$data[$i]['实际开始日期'];
			$nodecontent[$i]['实际结束日期']=$data[$i]['实际结束日期'];
			$nodecontent[$i]['需要天数']=$data[$i]['需要天数'];
			if($data[$i]['状态']=='0')
			{$color[$i]='#959595';}//#959595 gray
			if($data[$i]['状态']=='1')
			{$color[$i]='#23913a';}//#23913a green
			if($data[$i]['状态']=='2')
			{$color[$i]='#00a1e9';}//##00a1e9 blue
			if($data[$i]['开始识别']=='0')
			{$bef[$i]='';}
		    if($data[$i]['开始识别']=='1')
			{$bef[$i]='<a style="width:15px;height:16px;background-color:'.$color[$i].';border:1px solid #eee;
border-radius:2px 2px 2px 2px;font-size: 6px;margin:0px 2px 0px 2px;color:#fff;">&nbsp;E&nbsp;</a>';}//提前
			if($data[$i]['开始识别']=='2')
			{$bef[$i]='<a style="width:15px;height:16px;background-color:orange;border:1px solid #eee;
border-radius:2px 2px 2px 2px;font-size:6px;margin:0px 2px 0px 2px;color:#fff;">&nbsp;D&nbsp;</a>';}//延时
			if($data[$i]['结束识别']=='0')
			{$aft[$i]='';}
		    if($data[$i]['结束识别']=='1')
			{$aft[$i]='<a style="width:15px;height:16px;background-color:orange;border:1px solid #eee;
border-radius:2px 2px 2px 2px;font-size: 6px;margin:0px 2px 0px 2px;color:#fff;">&nbsp;D&nbsp;</a>';}//延时
			if($data[$i]['结束识别']=='2')
			{$aft[$i]='<a style="width:15px;height:16px;background-color:'.$color[$i].';border:1px solid #eee;
border-radius:2px 2px 2px 2px;font-size: 6px;margin:0px 2px 0px 2px;color:#fff;">&nbsp;E&nbsp;</a>';}//提前
		}
        $row=$t2->getpname_type($type);
        $this->view->pname=$row;
        $nodes= explode(',',$t2->getcurrentnodes($code));
        $currentnodes=array();
        $num=count($nodes);
        for($i=0;$i<$num;$i++)
        {
            $currentnodes[$i]['node']=$nodes[$i];
            $currentnodes[$i]['stat']=$t1->getstate($nodes[$i], $code);
        }
        //获取最后更新时间
        $updatetime=$t2->getupdatetime($code);
        $this->view->updatetime=$updatetime;
        $this->view->currentnodes=$currentnodes;
		$this->view->loginname=$_SESSION['loginuser'];
		$this->view->content=$nodecontent;
		$this->view->time=$time;
		$this->view->color=$color;
		$this->view->bef=$bef;
		$this->view->aft=$aft;
		$this->view->code=$code;
		//获取项目需要天数总和
		$this->view->totaldays=$t1->gettotaldaysbycode($code);
		//计算实际工作日期
		$projectdate=new projectdate();
		$workdays=0;
		for($i=0;$i<$nodecount;$i++)
		{
		    $workdays+=$projectdate->check_real_workdays($code, $i, $time);
		}
		$this->view->workdays=$workdays;
		if($type=="建筑类")
		{$this->render();}
		elseif($type=="道路类")
		{$this->render('main1');}
		elseif($type=="独立绿化类")
		{$this->render('main2');}
		elseif($type=="土地储备类")
		{$this->render('main3');}
		elseif($type=="河道（自筹）类")
		{$this->render('main4');}
		elseif($type=="河道（争取市级资金）类")
		{$this->render('main5');}
		
		}
    }
    
    //id 节点号，date当前日期
    public function checkstate1($id,$today,$code)
    {
        //判断该节点状态
        $t1=new t1();
        $t2=new t2();
        $state=0;
        $state=$t1->getstate($id,$code);//当前节点状态
        //获取当前节点
        $current_node=0;
        $current_node=$t2->getcurrentnodes($code);
        $current_nodes = explode(',',$current_node);//获取当前流程走到的节点
        file_put_contents("c:/x.log",$id.'state'.$state.' nodes'.$current_node."\r\n",FILE_APPEND);
        //未开始
        if($state==0)
        {
            $iscurrent=0;
            //是否是当前节点
            $num=count($current_nodes);
            for($i=0;$i<$num;$i++)
            {
                if($current_nodes[$i]==$id)
                {
                    $iscurrent=1;break;
                }
            }
			/*
			 if($iscurrent)//是
			{
			//check previous nodes finished totally
				$bef_nodes= explode(',',$t1->getbefs($id));
				$num1=count($bef_nodes);
				$allfinished='';
				for($i=0;$i<$num1;$i++)
				{
					if($t1->getstate($bef_nodes[$i])!=2)
					{$allfinished.=$bef_nodes[$i].",";}
				}
				if($allfinished=='')//all finished
				{$iscurrent=1;}
				else
				{
					$iscurrent=0;
					$allfinished=rtrim($allfinished, ',');
					//update current node;
					$t2->updatecn($allfinished);
				}
				file_put_contents("c:/2.log",'not finished'.$allfinished."\r\n",FILE_APPEND);
			}
			*/
            if($iscurrent)//是
            {
                //当前日期<=节点计划开始日期
                $startime=$t1->getstarttime($id,$code);//当前节点启始日期
					//$realstarttime=$t1->getrealendtime($id);//实际开始日期
					if(strtotime($today)<strtotime($startime))//可以提前开始--------!<= or <
					{
					    //check previous nodes finished totally
					    $bef_nodes= explode(',',$t1->getbefs($id,$code));
					    $num1=count($bef_nodes);
					    $allfinished='';
					    for($i=0;$i<$num1;$i++)
					    {
					        if($t1->getstate($bef_nodes[$i],$code)!=2)
					        {$allfinished.=$bef_nodes[$i].",";}
					    }
					    if($allfinished=='')//all finished
					    {
					        $t1->up_bef_att($id,$code,1);
					        //return "可以提前开始";
					    }
					}
					elseif(strtotime($today)==strtotime($startime))
					{
							;
					}
					else{
						$t1->up_bef_att($id,$code,2);
						//return "流程将延迟开始";
					}

            }
            else //不是当前节点
            {
                //当前日期<=节点计划开始日期
                $startime=$t1->getstarttime($id,$code);//当前节点启始日期
					//$realstarttime=$t1->getrealendtime($id);//实际开始日期
					if(strtotime($today)<=strtotime($startime))
					{
						$t1->up_bef_att($id,$code, 0);///anthor case?????????
						//return "未开始";
					}
					else {
						$t1->up_bef_att($id,$code, 2);
						//return "流程将延迟开始";
					}
            }
            $endtime=$t1->getendtime($id,$code);//当前节点终止日期
                if(strtotime($today)>strtotime($endtime))//日期大于计划结束日期
                {
                     $t1->up_bef_att($id,$code, 2);
                     $t1->up_aft_att($id, $code, 1);
                    //return "流程将延迟开始";
                }

        }
        //进行中
        if($state==1)
        {
			$endtime=$t1->getendtime($id,$code);//当前节点终止日期
            if(strtotime($today)>strtotime($endtime))
			{
				 $t1->up_aft_att($id,$code,1);
			}
        }
        //已完成
        if($state==2)
        {
            $endtime=$t1->getendtime($id,$code);//当前节点终止日期
            $realendtime=$t1->getrealendtime($id,$code);//实际终止日期
            if(strtotime($realendtime)==strtotime($endtime))//规定时间完成
            {
                $t1->up_aft_att($id,$code,0);//正常
            }
			elseif(strtotime($realendtime)<strtotime($endtime))
			{
			$t1->up_aft_att($id,$code,2);//提前
			}
            else
            {
                $t1->up_aft_att($id,$code,1);
            }
        }
    
    }
    //流程重启
    public function restartAction()
    {
        $code=$this->getRequest()->getParam("code","");//code
        $t1=new t1();
        $t1->restartwork($code);
        $t2=new t2();
        $t2->restartwork($code);
		$change_history=new change_history();
		$change_history->restartwork();
        $this->redirect('/main/main?code='.$code);
    }
    

    //设置流程计划时间
    public function setprojectAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
        
            $this->redirect('/index/index');
            exit();
        }
        $code=$this->getRequest()->getParam("code","");//code
        $t1=new t1();
        $res=$t1->getdata_by_code($code);
        $db=$t1->getAdapter();
        $sql=$db->select()->from('t1','*')->where('项目= ?',$code)->order('流程 ASC');
        $paginator = Zend_Paginator::factory($sql);
        $paginator->setItemCountPerPage(10);//每页行数
        $paginator->setPageRange(10);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;
        $this->view->code=$code;
        $this->view->res=$res;
        $this->view->loginname=$_SESSION['loginuser'];
	
		$this->render();
       
    }
    //ajax 显示节点参数表
    public function showpageAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
        $project=$this->getRequest()->getParam("project","");//project
        $page=$this->getRequest()->getParam("page","");//page
        $t1=new t1();
        $db=$t1->getAdapter();
        $sql=$db->select()->from('t1','*')->where('项目= ?',$project)->order('流程 ASC');
        $paginator = Zend_Paginator::factory($sql);
        $paginator->setItemCountPerPage(10);//每页行数
        $paginator->setPageRange(10);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;
        $this->view->code=$project;
        $this->render('ajaxpage');
    
    }

	
    //设置流程计划时间
    public function setproject1Action()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
        
            $this->redirect('/index/index');
            exit();
        }
        $code=$this->getRequest()->getParam("code","");//code
		$node=$this->getRequest()->getParam("node","");//node
        $t1=new t1();
        $res=$t1->getdata_by_code($code);
        $db=$t1->getAdapter();
        $sql=$db->select()->from('t1','*')->where('项目= ?',$code)->where('流程= ?',$node)->order('流程 ASC');
        $paginator = Zend_Paginator::factory($sql);
        $paginator->setItemCountPerPage(10);//每页行数
        $paginator->setPageRange(10);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;
        $this->view->code=$code;
		//$this->view->node=$node;
        $this->view->res=$res;
        $this->view->loginname=$_SESSION['loginuser'];
		$this->render('setproject1');
			
       
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
    //ajax显示模态框"节点相关文档上传"
    public function searchuploadAction()
    {
        date_default_timezone_set('PRC');
        if (!session_id())session_start();
        $node=$this->getRequest()->getParam("node","");//node
        $code=$this->getRequest()->getParam("project","");//p
        $upfile=new upfile();
        $filecontext=$upfile->findfile_bynode($code, $node);
        $this->view->filecontext=$filecontext;
        $this->render('detajax2');
    }
    //ajax显示mini下拉框
    public function showminiAction()
    {
        $node=$this->getRequest()->getParam("node","");//node
        $code=$this->getRequest()->getParam("code","");//node
		//file_put_contents("c:/xxxxxxx.log",$node."\r\n",FILE_APPEND);
        $node=substr($node, 2);//n_1
        $t1=new t1();
        if($node!=""&&$code!="")
        {
            $realstarttime=$t1->getrealstarttime($node,$code);
            $realendtime=$t1->getrealendtime($node,$code);
        }
        if($realstarttime=="")
            $realstarttime="未开始";
        if($realendtime=="")
            $realendtime="未开始";
        $this->view->startdate=$realstarttime;
        $this->view->enddate=$realendtime;
        $this->render('detminiajax');
        
    }

	    public function questionAction()
    {  
        $this->render('question'); 
    }

	public function up1Action()
	{
		        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
    
            $this->redirect('/index/index');
            exit();
        }
	    $node=$this->getRequest()->getParam("node","");//node
	    $code=$this->getRequest()->getParam("code","");//code
	    $starttime=$this->getRequest()->getParam("s","");//s
	    $endtime=$this->getRequest()->getParam("e","");//e
		$h_s=$this->getRequest()->getParam("h_s","");//h_s
		$h_e=$this->getRequest()->getParam("h_e","");//h_e
	    file_put_contents("c:/up1.log","n:".$node." c:".$code." s:".$starttime." e:".$endtime."\r\n",FILE_APPEND);
	    $t1=new t1();
	    $ans=0;
	    if($node!=""&&$code!="")
	    {
            $ans=$t1->up_pstartandend($node, $code, $starttime, $endtime);
			if($ans>0)
			{
			$history=new change_history();
			date_default_timezone_set('PRC'); //设置中国时区
			$time = time();
			$addtime = date("y-m-d H:i:s",$time); //2010-08-29;
			$history->add($code,$node,$_SESSION['loginuser']['username'],$addtime,$starttime,$endtime,$h_s,$h_e);
				if($_SESSION['loginuser']['position']=='董事长')
			{
			
				$t1->upc1_4($code,$node,'c4');
			}
			elseif($_SESSION['loginuser']['position']=='总经理')
			{
				$t1->upc1_4($code,$node,'c3');
			}
			elseif($_SESSION['loginuser']['position']=='副总经理')
			{
			$t1->upc1_4($code,$node,'c2');
			}
			elseif($_SESSION['loginuser']['position']=='经理')
			{
				$t1->upc1_4($code,$node,'c1');
			}

			}
	    }
	    file_put_contents("c:/up1.log",$ans."\r\n",FILE_APPEND);
	    $this->view->info=$ans;
	    $this->render('ajax');
	
	}
	//ganttajax
	public function getdataAction()
	{
	    $code=$this->getRequest()->getParam("code","");//code
	    $t1=new t1();
	    $ans1=array();
	    $txt="[";
	    if($code!="")
	    {
	        $ans=$t1->getdata_by_code($code);
	       
	        for($i=0;$i<count($ans);$i++)
	        {
	            /*
	            name: "1",
					desc: "Analysis",
					values: [{
						id: "t01",
						from: "/Date(1320192000000)/",
						to: "/Date(1322401600000)/",
						label: "Requirement Gathering",
						customClass: "ganttRed"
					}]
					*/
	           
	            $txt.="{name:\"".$ans[$i]['流程']."\",";
	            $txt.="desc:\"".$ans[$i]['节点名称']."\",";
	            $txt.="values:[{";
	            $txt.="from:\"".strtotime($ans[$i]['启始日期'])."000\","; 
	            $txt.="to:\"".strtotime($ans[$i]['终止日期'])."000\",";
	            $txt.="label:\"".$ans[$i]['节点名称']."\",";
	            if($i==count($ans)-1)
	            {$txt.="customClass: \"ganttGreen\"}]}]";}
	            else
	            {$txt.="customClass: \"ganttGreen\"}]},";}
	        }
	    }
	    //$info=json_encode($ans1);
	    $this->view->info=$txt;
	    $this->render('ajaxgantt');
	}
	//ajax查询更新记录
    public function showhisAction()
    {
        date_default_timezone_set('PRC');
        if (!session_id())session_start();
        $code=$this->getRequest()->getParam("code","");
		$node=$this->getRequest()->getParam("node","");
		$startorend=$this->getRequest()->getParam("sore","");//start or end
        $his=new change_history();
        $res=$his->getdata_by_node($node,$code);
        $this->view->historylist=$res;
		if($startorend=='s'){
        $this->render('showstarthisajax');
		}
		elseif($startorend=='e'){
        $this->render('showendhisajax');
		}
    }
     
    public function messageAction()
    {
        if (!session_id())session_start();
        if (!isset($_SESSION['loginuser']))
        {
        
            $this->redirect('/index/index');
            exit();
        }
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        $code=$this->getRequest()->getParam("code","");
        $updatetime=$this->getRequest()->getParam("t","");
        $t2=new t2();
        $isupdate=0;
        if($updatetime!=$t2->getupdatetime($code))
        {
            $isupdate=1;
        }
        //2010-08-29;
        $this->view->info="data:{\"update\":\"".$isupdate."\",\"data\":\"".$t2->getupdatetime($code)."\"}\n\n";
        $this->render('message');
    }

}

