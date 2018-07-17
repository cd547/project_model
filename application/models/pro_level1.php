<?php
require_once 'pro_count.php';
class pro_level1 extends Zend_Db_Table
{

	protected $_name='pro_level1';//表名
	/*
	 * 创建项目
	 * 接收用户提交的数据，将其入录数据表
	 * 接收数据应是数组，必须确保键名和数据表字段一致
	 * @param array $userData
	 */
	function create_project($Data)
	{
		$row=$this->createRow();
		if(count($Data)>0){
		    foreach($Data as $key=>$value)
		    {
		        switch($key)
		        {
		            default:
		                $row->$key=$value;
		        }
		    }
            date_default_timezone_set('PRC'); //设置中国时区
            $time = time();
            $createtime = date("Y-m-d H:i:s",$time);
		    $row->pro_createTime=$createtime;
            //$row->pro_updateTime=$createtime;
		    $row->save();
		    return $row->id;
		}
		else {
		    return null;
		}
	}

	//删除项目
    function deletepro($pro_num)
    {
        $db = $this->getAdapter();
        $where = $db->quoteInto('pro_num = ?', $pro_num);// 删除数据的where条件语句
        return $rows_affected = $this->delete($where);// 删除数据并得到影响的行数

    }

    //获取项目编号最大值
    function getmaxpronum()
    {
        $db = $this->getAdapter();
        $sql = $db->quoteInto('SELECT MAX(pro_num) as maxnum FROM pro_level1','');
        $result = $db->query($sql);
        $array = $result->fetchAll();
        return $array[0]['maxnum'];
    }




}
