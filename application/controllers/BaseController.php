<?php
//抽象一个父类，专门供其它控制器来继承
class BaseController extends Zend_Controller_Action
{
	public function init()
	{
		//初始化数据库适配器
		$url= constant("APPLICATION_PATH").DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'application.ini';
		$dbconfig=new Zend_Config_Ini($url,"mysql");
		$db=Zend_Db::factory($dbconfig->db);
		$db->query('SET NAMES UTF8');
		Zend_Db_Table::setDefaultAdapter($db);
	}
	
	 public function backtable($table_name)
	{
        $url= constant("APPLICATION_PATH").DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'application.ini';
        $dbconfig=new Zend_Config_Ini($url,"mysql");
	    $db_name=$dbconfig->db.params.dbname;
	    $db_username=$dbconfig->db.params.username;
	    $db_password=$dbconfig->db.params.password;
	    date_default_timezone_set('PRC'); //设置中国时区
	    $time = time();
	    $bkname=$table_name."_".$time.".sql";
	    $exec="D:/wamp64/bin/mysql/mysql5.7.14/bin/mysqldump -u$db_username -p$db_password $db_name $table_name > c:/$bkname";
	    exec($exec);
	    
	}
}
?>