<?php
require_once 't2.php';
class change_history extends Zend_Db_Table

{
    protected $_name='change_history';//table name
	//更新状态
	function updatefew($flow,$time1,$stat,$show)
	{
	    $data=array(
	       '终止日期'=>$time1,
	       '状态'=>$stat,
	       '提示'=>$show
	    );
	    //try
	    {
	        //更新
	        $where="流程=".$flow;
	        $num=$this->update($data, $where);
	        if($num>0)
	        {
	            return true;
	        }
	        else {return false;}
	    }
	    //catch (Exception $exp)
	    //{return $exp;}
	}
	
	
	 function add($project,$node,$username,$time,$start,$end,$h_start,$h_end)
    {
        $data=array(
            'id'=>'',
            '项目'=>$project,
            '节点'=>$node,
            '操作人'=>$username,
            '更新时间'=>$time,
            '计划开始'=>$start,
            '计划结束'=>$end,
            '历史计划开始'=>$h_start,
			'历史计划结束'=>$h_end
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

		function getdata_by_node($node,$code)
	{
	    //$state=0;
	    $db=$this->getAdapter();
	    $sql = $db->quoteInto('SELECT * FROM change_history WHERE 节点 = ?',$node).$db->quoteInto(' AND 项目  = ?',$code).$db->quoteInto(' ORDER BY 更新时间  DESC;','');
	    $res=$db->query($sql)->fetchAll();
	    //$state=$res[0];
	    return $res;
	}

	//初始化
	function restartwork()
	{
	    $this->getAdapter()->query('TRUNCATE TABLE `' . $this->_name . '`');
		 return $this;
	}
}
?>