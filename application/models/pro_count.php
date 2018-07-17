<?php
class pro_count extends Zend_Db_Table
{

	protected $_name='pro_count';//表名

	function create_project_count()
	{
	    $num=null;
        date_default_timezone_set('PRC'); //设置中国时区
        $time = time();
        $this_year = date("Y",$time);
        $rows_affected=0;
        $db=$this->getAdapter();
	    //检查当年是否存在
        if($this->check_year($this_year)) {
            //存在：更新
            //查找
            $row=$this->fetchRow("pro_year=".$this_year)->toArray();
            $data=array(
                'num' =>$row['num']+1,
                'create_time' =>date("Y-m-d H:i:s",$time)
            );
            $where=" pro_year='".$this_year."'";
            $rows_affected=$this->update($data, $where);
            if($rows_affected>0)
            {
                $num=$row['num']+1;
            }
        }
        else{
            //不存在：创建
            $row = array (
                'pro_year'   => $this_year,
                'num' =>1,
                'create_time' =>date("Y-m-d H:i:s",$time)
            );
            $rows_affected = $db->insert($this->_name, $row);
            if($rows_affected>0){
                $num=1;
            }
        }
       return $num;
	}
    //检查年份
	function check_year($year)
    {
        $state=false;
        $db=$this->getAdapter();
        $sql = $db->quoteInto('SELECT * FROM pro_count WHERE pro_year = ?',$year);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {$state=true;}//存在
        return $state;
    }
    //



}
