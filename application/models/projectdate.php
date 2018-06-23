<?php
require_once 't1.php';
class projectdate extends Zend_Db_Table
{
	protected $_name='工程日历';//table name
	/*
	 * $project:项目
	 * $date：日期
	 * $rest：是否休息
	 * $reason:休息理由
	 */
	function createday($project,$date,$rest,$reason)
	{
	        $data=array(
            'id'=>'',
            '项目'=>$project,
            '日期'=>$date,
            '休息'=>$rest,
            '休息原因'=>$reason
        );
        //try
        {
            //插入
            $num=$this->insert($data);
            if($num>0)
            {
                return $num;
            }
            else {return 0;}
        }
        //catch (Exception $exp)
        //{return $exp;}
	}
	//项目工程日期是否存在
	function checkprojectdate($project,$date)
	{
	    $db=$this->getAdapter();
	    $sql=$db->quoteInto("SELECT * FROM 工程日历  WHERE 项目=?",$project).$db->quoteInto(" AND 日期=?", $date);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {
	        return $res;
	    }
	    else {
	        return null;
	    }
	}

	//获取项目工程日期
	function getprojectdates($project)
	{
	    $db=$this->getAdapter();
	    $sql=$db->quoteInto("SELECT * FROM 工程日历  WHERE 项目=?",$project);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {
	       return $res;
	    }
	    else {
	        return null;
	    }
	}
	//获取项目工程日期休息日
	function getproject_tried_dates($project)
	{
	    $db=$this->getAdapter();
	    $sql=$db->quoteInto("SELECT * FROM 工程日历  WHERE 项目=?",$project).$db->quoteInto(" AND 休息=?", 1);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {
	        return $res;
	    }
	    else {
	        return null;
	    }
	
	}
	//获取项目工程日期工作日
	function getproject_work_dates($project)
	{
	    $db=$this->getAdapter();
	    $sql=$db->quoteInto("SELECT * FROM 工程日历  WHERE 项目=?",$project).$db->quoteInto(" AND 休息=?", 0);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {
	        return $res;
	    }
	    else {
	        return null;
	    }
	
	}
	//工程日期里是否有该条日期，返回如果不在记录中，需要判断是否是周日周六，如果查到就看返回值：1休息日，0工作日
	function checkday($project,$date)
	{
	    $db=$this->getAdapter();
	    $sql=$db->quoteInto("SELECT * FROM 工程日历  WHERE 项目=?",$project).$db->quoteInto(" AND 日期=?", $date);
	    $res=$db->query($sql)->fetchAll();
	    if(count($res)>0)
	    {
	        return $res[0]['休息'];
	    }
	    else
	    {
	        $datestemp=strtotime($date);
	        if(date('w',$datestemp)==6||date('w',$datestemp)==0)
	        {
	            return 1;//holiday
	        }
	        else
	        {
	            return 0;//workday
	        }
	    }
	}
	//删除
	public function delete_day($date,$project)
	{
	    $num=0;
	    $db=$this->getAdapter();
	    $where=$db->quoteInto('项目=?', $project).$db->quoteInto(" AND 日期=?", $date);
	    $num=$this->delete($where);
	    return $num;
	}
	
	///高级功能
	
	public $temparr=array();//路径数组
	public $nodesdata=array();//项目节点信息
	public $lastnode;//最后的节点
	public $nodecontent=array();//各个流程路径下项目节点日期
	public $holiday=array();//项目所有休息日
	public $keyrout=array();//系统关键路径
	public $caldate=array();//计算后的所有节点的日期
	//获取项目节点及后置节点信息，为了后面加快速度
	public function initnodedata($project)
	{
	    //清理
	    $temparr=null;
	    $nodesdata=null;
	    $lastnode=null;
	    $nodecontent=null;
	    $holiday=null;
	    ///////////////////
	    $t1=new t1();
	    $res=$t1->getdata_by_code($project);
	    $arr=array();
	    $n=count($res);
	    for($i=0;$i<$n;$i++)
	    {
	        $arr[$i]['node']=$res[$i]['流程'];
	        $arr[$i]['next']=$res[$i]['后置任务'];
	        if($res[$i]['后置任务']=='END'){$this->lastnode=$res[$i]['流程'];}
	        $arr[$i]['needdays']=$res[$i]['需要天数'];
	        $arr[$i]['start']=$res[$i]['启始日期'];
	        $arr[$i]['end']=$res[$i]['终止日期'];
	    }
	    $this->nodesdata=$arr;
	    //列出项目所有休息日
	    $db1=$this->getAdapter();
	    $sql=$db1->quoteInto("SELECT * FROM 工程日历  WHERE 项目=?",$project);
	    $this->holiday=$db1->query($sql)->fetchAll();
	}
	
	//读取内存里，工程日期里是否有该条日期，返回如果不在记录中，需要判断是否是周日周六，如果查到就看返回值：1休息日，0工作日
	function checkday_v2($project,$date)
	{
	    $res=null;
	    $n=count($this->holiday);
	    for($i=0;$i<$n;$i++)
	    {
	        if( $this->holiday[$i]['日期']==$date)
	        { $res= $this->holiday[$i]['休息'];
	        break;
	        }
	    }
	    if(count($res)>0)
	    {
	        return $res;
	    }
	    else
	    {
	        $datestemp=strtotime($date);
	        if(date('w',$datestemp)==6||date('w',$datestemp)==0)
	        {
	            return 1;//holiday
	        }
	        else
	        {
	            return 0;//workday
	        }
	    }
	}
	//获取$node的后置节点
	public function getnodeafters($node,$project)
	{
	    $res=null;
	    $n=count($this->nodesdata);
	    for($i=0;$i<$n;$i++)
	    {
	        if($this->nodesdata[$i]['node']==$node)
	        { $res=$this->nodesdata[$i]['next'];
	           break;
	        }
	    }
	    return $res;
	}
	//获取$node的需要天数
	public function getneeddays($node,$project)
	{
	    $res=null;
	    $n=count($this->nodesdata);
	    for($i=0;$i<$n;$i++)
	    {
	        if($this->nodesdata[$i]['node']==$node)
	        { $res=$this->nodesdata[$i]['needdays'];
	        break;
	        }
	    }
	    return $res;
	}
	//获取$node的结束日期
	public function getendday($node,$project)
	{
	    $res=null;
	    $n=count($this->nodesdata);
	    for($i=0;$i<$n;$i++)
	    {
	        if($this->nodesdata[$i]['node']==$node)
	        { $res=$this->nodesdata[$i]['end'];
	        break;
	        }
	    }
	    return $res;
	}
	//更新$this->nodesdata中结束日期
	public function updateenddate($node,$project,$date)
	{
	    $res=null;
	    $n=count($this->nodesdata);
	    for($i=0;$i<$n;$i++)
	    {
	        if($this->nodesdata[$i]['node']==$node)
	        { 
	            $this->nodesdata[$i]['end']=$date;
	            break;
	        }
	    }
	}
	//根据键值排序
public function arr_sort($arr,$sort,$v){    //$arr->数组   $sort->排序顺序标志   $value->排序字段
 
    if($sort == "0"){                   //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
            $sort = "SORT_ASC";
    }elseif ($sort == "1") {
            $sort = "SORT_DESC";
    }
      
    foreach($arr as $uniqid => $row){  
        foreach($row as $key=>$value){                     
                $arrsort[$key][$uniqid] = $value;
            }  
        }  
        if($sort){
        array_multisort($arrsort[$v], constant($sort), $arr);  
    }       
     return $arr;
}
	///列出所有路径
	//$node: 开始节点 $rout:默认值空  $this->temparr最终返回到该数组
	public function getnext($project,$node,$rout)
	{
	    //$t1=new t1();
	    if($node!=$this->lastnode)
	    {
	      //  $after_nodes=explode(',',$t1->getafters($node, $project));
	        $after_nodes=explode(',',$this->getnodeafters($node, $project));
	        $num=count($after_nodes);
	        if(!($after_nodes[0]==null)){
	            for($i=0;$i<$num;$i++)
	            {
	                if($i==0){$rout.=$node.",";}//去重复节点
	                //file_put_contents("/Users/cd547/rout.log",$node."--".$i."<-".$rout."->"."\r\n",FILE_APPEND);
	                $this->getnext($project,$after_nodes[$i],$rout);
	            }
	        }
	        else//死路
	        {
	            $rout.=$node;
	            //   file_put_contents("/Users/cd547/rout.log",$rout."\r\n",FILE_APPEND);
	            $arr0=explode(',',$rout);
	            $this->temparr[]=$arr0;//追加数组
	        }
	    }
	    else
	    {
	        $rout.=$node;
	        $arr0=explode(',',$rout);
	        $this->temparr[]=$arr0;//追加数组
	        // file_put_contents("/Users/cd547/rout.log",$rout."\r\n",FILE_APPEND);
	    }
	}
	
	//计算单条路径日期
	//$arr 单条路径数组 $start_date：开始日期
	public function rout_date($project,$arr,$start_date)
	{
	    $enddate=null;
	    $temp_enddate=null;
	    //$t1=new t1();
	    $num=count($arr);//count of nodes
	    $totle_days=0;
	    $date=$start_date;
	    $strat_enddate=array();
	    $content="共".$num.'个节点'."\r\n";
	    $i=0;
	    while($i<$num)
	    {
	        $enddate['id']=$i;
	        //$res=$t1->getdata_by_node($arr[$i],$project);
	         $need_days=$this->getneeddays($arr[$i], $project);//获取当前节点的所需天数
	         if($need_days>0)
	       {
	        while($this->checkday_v2($project,$date)==1)//休息日跳过
	        {
	            $date=date('Y-m-d',strtotime("$date +1 day"));
	        }
	        $content.='     节点'.$arr[$i].'需要天数:'.$need_days.' 开始日期：'.$date;
            $temp_strat_date=$date;
	        $j=0;
	        while($j<$need_days)
	        {
	           ($this->checkday_v2($project,$date)==0)? $totle_days++:$j--;
	            $date=date('Y-m-d',strtotime("$date +1 day"));
	            $j++;
	        }
	        $date=date('Y-m-d',strtotime("$date -1 day"));
	        $content.=' 结束日期：'.$date."\r\n";
	        $temp_enddate=$date;
	        $date=date('Y-m-d',strtotime("$date +1 day"));
	        $strat_enddate[]=[$temp_strat_date,$temp_enddate];
	       }
	       else 
	       {
	           while($this->checkday_v2($project,$date)==1)//休息日跳过
	           {
	               $date=date('Y-m-d',strtotime("$date +1 day"));
	           }
	           $content.='     节点'.$arr[$i].'需要天数:'.$need_days.' 开始日期：'.$date;
	           $temp_strat_date=$date;
	           
	           $content.=' 结束日期：'.$date."\r\n";
	           $temp_enddate=$date;
	           $strat_enddate[]=[$temp_strat_date,$temp_enddate];
	       }
	        $i++;
	    }
	    $content.='共计工作日：'.$totle_days."\r\n";
	    
	    $enddate['rout']=$arr;
	    $enddate['date']=$temp_enddate;
	    $enddate['totle_days']=$totle_days;
	    $enddate['start_end']=$strat_enddate;
	
	    file_put_contents("c:/day.log",$content."\r\n",FILE_APPEND);
	    return $enddate;
	}
	//获取最长路径及日期
	public function keyrouts($project,$startnode,$date)
	{  
	    $rout=null;
	    {
	       $this->initnodedata($project);
	       $this->getnext($project,$startnode,$rout);
	    }
	    $num=count($this->temparr);
	    $enddate=array();
	    $maxdate=$date;
	    $t1 = microtime(true);
	    date_default_timezone_set('PRC'); //设置中国时区
	    $time = time();
	    $starttime = date("y-m-d H:i:s",$time);
	    $i=0;
	    while($i<$num)
	    //for($i=0;$i<$num;$i++)
	    {
	        $newenddate=null;
	        $newenddate=$this->rout_date($project, $this->temparr[$i], $date);
	        if(strtotime($maxdate)<=strtotime($newenddate['date']))
	        {
	            $enddate[]=$newenddate;
	            $maxdate=$newenddate['date'];
	        }
	        $i++;
	    }
	    $t2 = microtime(true);
	    file_put_contents("c:/time.log", $starttime."--getnext耗时:".round($t2-$t1,3)."秒"."\r\n",FILE_APPEND);
	    $totalendate=array();
	    $endn=count($enddate);
	    for($i=0;$i<$endn;$i++)
	    {
	        ($enddate[$i]['date']==$maxdate)?$totalendate[]=$enddate[$i]:null;
	    }
	    $n=count($totalendate);
	    $max_rout_info='共'.$n."条关键路径<br>";
	    for($i=0;$i<$n;$i++)
	    {
	        
	        $m=count($totalendate[$i]['rout']);
	        $max_rout_info.='第'.($i+1).'条，共'.$m.'个节点最后结束日期:'.$totalendate[$i]['date'].',&nbsp;共耗时'.$totalendate[$i]['totle_days'].'个工作日 <br>&nbsp;&nbsp;路径:';
	        $j=0;
	        while($j<$m)
	        {
	            ($j<$m-1)?
	            $max_rout_info.='<span class="btn btn-primary btn-xs" style="margin-top:3px;">'.$totalendate[$i]['rout'][$j].'【'.$totalendate[$i]['start_end'][$j][0].','.$totalendate[$i]['start_end'][$j][1].'】</span> => '
	            :$max_rout_info.='<span class="btn btn-primary btn-xs" style="margin-top:3px;">'.$totalendate[$i]['rout'][$j].'【'.$totalendate[$i]['start_end'][$j][0].','.$totalendate[$i]['start_end'][$j][1].'】</span><br>';
	            $this->keyrout[$i][]=['node'=>$totalendate[$i]['rout'][$j],'start'=>$totalendate[$i]['start_end'][$j][0],'end'=>$totalendate[$i]['start_end'][$j][1]];
	                $j++;
	        }
	        $max_rout_info.="<br>";
	    }
	   // var_dump($this->keyrout[0]);
	    //更新nodesdata里的日期
	    for($i=0;$i<count($this->keyrout[0]);$i++)
	    {
	        for($j=0;$j<count($this->nodesdata);$j++)
	        if($this->keyrout[0][$i]['node']==$this->nodesdata[$j]['node'])
	        {
	            $this->nodesdata[$j]['start']=$this->keyrout[0][$i]['start'];
	            $this->nodesdata[$j]['end']=$this->keyrout[0][$i]['end'];
	            break;
	        }

	    }
	   // file_put_contents("c:/rout.log", var_dump($this->keyrout)."\r\n",FILE_APPEND);
	   //$this->inputdate($project);
	   
	    return $max_rout_info;
	}
	//计算节点开始结束日期:$date:前一节点的结束日期
	function setstart_end($project,$need_days,$date)
	{
	    $res=array();
	    if($need_days>0)
	    {
	        $date=date('Y-m-d',strtotime("$date +1 day"));
	        while($this->checkday_v2($project,$date)==1)//休息日跳过
	        {
	            $date=date('Y-m-d',strtotime("$date +1 day"));
	        }
	        $res['start']=$date;
	        $j=0;
	        while($j<$need_days)
	        {
	            ($this->checkday_v2($project,$date)==0)? '':$j--;
	            $date=date('Y-m-d',strtotime("$date +1 day"));
	            $j++;
	        }
	        $date=date('Y-m-d',strtotime("$date -1 day"));
	        //$content.=' 结束日期：'.$date."\r\n";
	        $res['end']=$date;
	    }
	    else
	    {
	        $res['start']=$date;
	        $res['end']=$date;
	    }
	    return $res;
	}
	//after keyrouts
	public function inputdate($project,$startnode,$startdate)
	{
	    $this->keyrouts($project,$startnode,$startdate);
	    $node_deal=array();//已经解决的节点
	    $node_deal=$this->keyrout[0];//关键路径节点导入已完成数组
	    $node_deal_node=array();
	    for($i=0;$i<count($node_deal);$i++)
	    {
	        $node_deal_node[]=$this->keyrout[0][$i]['node'];
	    }
	    //var_dump($node_deal_node);
	    $node=array();//所有节点
	    $t1=new t1();
	    $res=$t1->getdata_by_code($project);
	    $n=count($res);
	    for($i=0;$i<$n;$i++)
	    {
	        if($res[$i]['前置任务']!=''&&$res[$i]['后置任务']!='')//去除孤立的空节点
	        {
	           $node[]=['node'=>$res[$i]['流程'],
	            'start'=>$res[$i]['启始日期'],
	            'end'=>$res[$i]['终止日期'],
	            'befor'=>$res[$i]['前置任务'],
	            'after'=>$res[$i]['后置任务'],
	            'days'=>$res[$i]['需要天数']
	        ];
	        }
	    }
	    //剩下节点
	    $node_undeal=array();
	    $n=count($node);//所有
	    $m=count($node_deal);
	    $node_undeal=$node;
       for($i=0;$i<$m;$i++)
       {
           for($j=0;$j<$n;$j++)
           {
               if($node[$j]['node']==$node_deal[$i]['node'])
               {
                   unset($node_undeal[$j]);break;
               }
           } 
       }
       sort($node_undeal);//重新排序下标
       //var_dump($node_undeal);
	    while(count($node_undeal)>0)
	    {
	        $num=count($node_undeal);
	        //file_put_contents("c:/un.log",$num."：\r\n",FILE_APPEND);
	        for($i=0;$i<$num;$i++)
	        {
	            //未处理节点的前置任务
	           // file_put_contents("c:/before.log",$num."：\r\n",FILE_APPEND);
	           $before= explode(',',$node_undeal[$i]['befor']);
	           //file_put_contents("c:/before.log",$node_undeal[$i]['befor']."\r\n",FILE_APPEND);
	           if(count($before)==1)
	           {
	               //file_put_contents("c:/befor.log",$before[0]."\r\n",FILE_APPEND);
	               //单个:判断节点是否在已经解决的节点
	              
	               if(in_array($before[0],$node_deal_node))
	               {
	                   //file_put_contents("c:/jj.log",count($node_undeal)."\r\n",FILE_APPEND);
	                  $date=$this->getendday($before[0], $project); //获取结束日期
	                  $need_days=$this->getneeddays($node_undeal[$i]['node'], $project);//获取当前节点的所需天数
	                 // file_put_contents("c:/ans.log",$node_undeal[$i]['node'].":".$date.' '.$need_days."\r\n",FILE_APPEND);
	                  $res=$this->setstart_end($project, $need_days, $date);
	                  // $node_undeal[$i]['start']=$res['start'];
	                  // $node_undeal[$i]['end']=$res['end'];
                       //加入到已处理的
	                  $node_deal[]=['node'=>$node_undeal[$i]['node'],'start'=>$res['start'],'end'=>$res['end']];
	                  //同时更新更新$node_deal_node
	                  $node_deal_node[]=$node_undeal[$i]['node'];
	                  //更新$this->nodesdata，因为查询getendday里日期还没更新
	                   $this->updateenddate($node_undeal[$i]['node'], $project, $res['end']);
	                  //在未处理中去除该条
	                  //file_put_contents("c:/deal.log",$node_undeal[$i]['node']."--".$res['start']."-".$res['end']."\r\n",FILE_APPEND);
	                  unset($node_undeal[$i]);
	                  // file_put_contents("c:/undeal.log",count($node_undeal)."\r\n",FILE_APPEND);
	               }
	           }
	           else
	           {
	               //多个:判断节点是否在已经解决的节点
	               //判断前置节点是否都在$node_deal中
	               $allin=true;
	               $nb=count($before);
	               for($k=0;$k<$nb;$k++)
	               {
	                  $allin= $allin&&in_array($before[$k],$node_deal_node);
	               }
	              if($allin)
	              {
	                  //找前置节点中结束日期最大的那个
	                  $maxdate=$startdate;
	                   for($k=0;$k<$nb;$k++)
	                   {
	                      $end= $this->getendday($before[$k], $project);
	                      if(strtotime($maxdate)<=strtotime($end))
	                      {
	                             $maxdate=$end;
	                       }
	                   }
	                   $date=$maxdate;
	                   $need_days=$this->getneeddays($node_undeal[$i]['node'], $project);//获取当前节点的所需天数
	                  // file_put_contents("c:/ans.log",$node_undeal[$i]['node'].":".$date.' '.$need_days."\r\n",FILE_APPEND);
	                   $res=$this->setstart_end($project, $need_days, $date);
	                   // $node_undeal[$i]['start']=$res['start'];
	                   // $node_undeal[$i]['end']=$res['end'];
	                   //加入到已处理的
	                   $node_deal[]=['node'=>$node_undeal[$i]['node'],'start'=>$res['start'],'end'=>$res['end']];
	                   //同时更新更新$node_deal_node
	                   $node_deal_node[]=$node_undeal[$i]['node'];
	                   //更新$this->nodesdata，因为查询getendday里日期还没更新
	                   $this->updateenddate($node_undeal[$i]['node'], $project, $res['end']);
	                   //在未处理中去除该条
	                   //file_put_contents("c:/deal.log",$node_undeal[$i]['node']."--".$res['start']."-".$res['end']."\r\n",FILE_APPEND);
	                   unset($node_undeal[$i]);
	                   //file_put_contents("c:/undeal.log",count($node_undeal)."\r\n",FILE_APPEND); 
	              }
	           }
	        }
	        sort($node_undeal);
	    }
	  //  var_dump($node_undeal);
	   
	    $node_deal=$this->arr_sort($node_deal,0,'node');//根据node字段重新排序
	   // var_dump($node_deal);
	   $this->caldate=$node_deal;
	    return  $node_deal;
	}
	
	//判断项目节点的实际工作天数
	public function check_real_workdays($project,$node,$today)
	{
	    $workdays=0;
	    $t1=new t1();
	    //实际开始日期
	    $starttime=$t1->getrealstarttime($node, $project);
	    //实际结束日期
	    $endtime=$t1->getrealendtime($node, $project);
	    if($starttime=="")
	    {return $workdays;}
	    elseif($starttime!=""&&$endtime=="")
	    {
	     //$today-$starttime  
	      $date = $starttime;
	      $workdays=0;
	       while(strtotime($date)<strtotime($today))
	       {
	           if($this->checkday($project,$date)==1)//休息日跳过
	           {$date=date('Y-m-d',strtotime("$date +1 day"));}
	           else {
	               $date=date('Y-m-d',strtotime("$date +1 day"));
	               $workdays++;
	           }
	       }
	       return $workdays;
	    }
	    elseif($starttime!=""&&$endtime!="")
	    {
	        //$endtime-$starttime
	       
	        $date = $starttime;
	        $workdays=0;
	        while(strtotime($date)<=strtotime($endtime))
	        {
	            if($this->checkday($project,$date)==1)//休息日跳过
	            {$date=date('Y-m-d',strtotime("$date +1 day"));}
	            else {
	                $date=date('Y-m-d',strtotime("$date +1 day"));
	                $workdays++;
	            }
	        }
	        return $workdays;
	    }
	    
	    
	}
}
?>