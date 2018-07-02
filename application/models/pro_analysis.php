<?php
class pro_analysis extends Zend_Db_Table
{
	protected $_name='pro_analysis';//表名

    //查询项目相关的分析信息
    function getAnalysisbyPro_num($pro_num)
    {
        $db=$this->getAdapter();
        $sql=$db->quoteInto("SELECT * FROM users WHERE pro_num=?",$pro_num);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res;
        }
        else {
            return null;
        }
    }

}
