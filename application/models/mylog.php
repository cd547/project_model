<?php
require_once 'users.php';
class mylog extends Zend_Db_Table

{
    protected $_name='mylog';//table name

    function getmanager($dep)
    {
        $db=$this->getAdapter();
        $sql=$db->quoteInto("SELECT * FROM users WHERE dep=?",$dep).$db->quoteInto(" AND position=?",'部门经理');
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res[0];
        }
        else {
            return null;
        }
    }
    
    function savemylog($username,$userid,$title,$dep,$content,$manager,$managername,$creattime)
    {
        $data=array(
            'id'=>'',
            'username'=>$username,
            'userid'=>$userid,
            'title'=>$title,
            'dep'=>$dep,
            'content'=>$content,
            'manager'=>$manager,
            'managername'=>$managername,
            'save'=>1,
            'post'=>0,
            'checked'=>0,
            'creattime'=>$creattime
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
    
    function updatemylog($id,$username,$userid,$title,$dep,$content,$manager,$managername,$creattime)
    {
        $data=array(
            'username'=>$username,
            'userid'=>$userid,
            'title'=>$title,
            'dep'=>$dep,
            'content'=>$content,
            'manager'=>$manager,
            'managername'=>$managername,
            'save'=>1,
            'post'=>0,
            'checked'=>0,
            'creattime'=>$creattime
        );
        {
            //插入
            $where="id=$id";
            $num=$this->update($data, $where);
            if($num>0)
            {
                return $num;
            }
            else { return 0;}
        }
    }
    
    function updatepostmylog($id,$username,$userid,$title,$dep,$content,$manager,$managername,$creattime)
    {
        $data=array(
            'username'=>$username,
            'userid'=>$userid,
            'title'=>$title,
            'dep'=>$dep,
            'content'=>$content,
            'manager'=>$manager,
            'managername'=>$managername,
            'save'=>0,
            'post'=>1,
            'checked'=>0,
            'creattime'=>$creattime
        );
        {
            //插入
            $where="id=$id";
            $num=$this->update($data, $where);
            if($num>0)
            {
                return $num;
            }
            else { return 0;}
        }
    }
    
    function checklog($id,$checktime)
    {
        $data=array(
            'checked'=>1,
            'checktime'=>$checktime
        );
        {
            //插入
            $where="id=$id";
            $num=$this->update($data, $where);
            if($num>0)
            {
                return $num;
            }
            else { return 0;}
        }
    }
    
    function postmylog($username,$userid,$title,$dep,$content,$manager,$managername,$creattime)
    {
        $data=array(
            'id'=>'',
            'username'=>$username,
            'userid'=>$userid,
            'title'=>$title,
            'dep'=>$dep,
            'content'=>$content,
            'manager'=>$manager,
            'managername'=>$managername,
            'save'=>0,
            'post'=>1,
            'checked'=>0,
            'creattime'=>$creattime
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
    //查询保存的工作日志
    function findsave($userid)
    {
        $db=$this->getAdapter();
        $sql = $db->quoteInto('SELECT * FROM mylog WHERE userid= ?',$userid).$db->quoteInto(' AND save  = ? ORDER BY creattime DESC','1');
        $res=$db->query($sql)->fetchAll();
        //$state=$res[0];
        return $res;
    }
    //查询待审批的工作日志
    function findpost($manager,$username)
    {
        $db=$this->getAdapter();
        $sql=null;
        $sql = $db->quoteInto('SELECT * FROM mylog WHERE manager= ?',$manager).$db->quoteInto(' AND checked  = ?','0').$db->quoteInto(' AND post  = ? ORDER BY creattime DESC','1');
        if($username!='')
            $sql = $db->quoteInto('SELECT * FROM mylog WHERE manager= ?',$manager).$db->quoteInto(' AND username LIKE ?',"%".$username."%").$db->quoteInto(' AND checked  = ?','0').$db->quoteInto(' AND post  = ? ORDER BY creattime DESC','1');
        $res=$db->query($sql)->fetchAll();
        //$state=$res[0];
        return $res;
    }
    
    //查询我的已经审批的工作日志
    function findmylog($userid)
    {
        $db=$this->getAdapter();
        $sql = $db->quoteInto('SELECT * FROM mylog WHERE userid= ?',$userid).$db->quoteInto(' AND checked  = ?','1').$db->quoteInto(' AND post  = ? ORDER BY creattime DESC','1');
            $res=$db->query($sql)->fetchAll();
            //$state=$res[0];
            return $res;
    }
    
    
    //通过id查询问题
    function findmylog_id($id)
    {
        $db=$this->getAdapter();
        $sql = $db->quoteInto('SELECT * FROM mylog WHERE id= ?',$id);
        $res=$db->query($sql)->fetchAll();
        //$state=$res[0];
        return $res;
    }
    
    function del($id)
    {
        $db=$this->getAdapter();
        $where=$db->quoteInto('id=?', $id);
        $num=$this->delete($where);
        if($num>0)
        {
            return $num;
        }
        else {return 0;}
    }
    
    function handleproblem_id($id,$handle_title,$handle_content,$handler,$date)
    {
        $data=array(
            '解决方案标题'=>$handle_title,
            '解决方案简述'=>$handle_content,
            '简述人'=>$handler,
            '实际解决日期'=>$date,
            '是否解决'=>'1'
        );
        //try
        {
            //插入
            $where="id=$id";
            $num=$this->update($data, $where);
            if($num>0)
            {
                return true;
            }
            else {return false;}
        }
    }
    //通过申请协助人和项目名称查询当前项目问题
    function searchproblemsnum_helper_project($helper,$project)
    {
        $db=$this->getAdapter();
        $sql = $db->quoteInto('SELECT count(*) as num,节点 FROM problem WHERE 项目名称= ?',$project).$db->quoteInto(" AND 申请协助人=? AND 是否解决=0 GROUP BY 节点",$helper);
        $res=$db->query($sql)->fetchAll();
        //$state=$res[0];
        return $res;
    }
    //获取部门领导
    function getmanagersByDep($dep)
    {
        $db=$this->getAdapter();
        //$sql=$db->quoteInto("SELECT * FROM users WHERE dep=? ORDER BY CONVERT(username USING GBK) ASC",$dep);
        $sql=$db->quoteInto("SELECT * FROM users WHERE dep=? AND position NOT IN('业务员','系统维护员') ORDER BY xh ASC",$dep);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return $res;
        }
        else {
            return null;
        }
    
    }
    //是否是管理员
    function isnotmanager($id)
    {
        $db=$this->getAdapter();
        //$sql=$db->quoteInto("SELECT * FROM users WHERE dep=? ORDER BY CONVERT(username USING GBK) ASC",$dep);
        $sql=$db->quoteInto("SELECT * FROM users WHERE id=? AND position NOT IN('业务员','系统维护员')",$id);
        $res=$db->query($sql)->fetchAll();
        if(count($res)>0)
        {
            return true;
        }
        else {
            return false;
        }
    }
    //获取所有部门
    function showdeps()
    {
        $db=$this->getAdapter();
        //$sql=$db->quoteInto("SELECT * FROM users WHERE dep=? ORDER BY CONVERT(username USING GBK) ASC",$dep);
        $sql=$db->quoteInto("SELECT dep FROM users WHERE 1=? GROUP BY dep",1);
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
?>