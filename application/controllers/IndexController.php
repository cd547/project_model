<?php
require_once 'BaseController.php';
class IndexController extends BaseController
{
 /*
  * 登录动作：如有session记录则直接跳转到MainControlller的indexAction，
  * 如果没有就显示登录页面
  */
    public function indexAction()
    {
		$t1 = microtime(true);
		$t2=null;
    	date_default_timezone_set('PRC'); //设置中国时区
    	$time = time();
    	$start_time = date("y-m-d H:i:s",$time);

    	if (!isset($_SESSION['loginuser']))
    	{
            $this->render('index');
            $t2 = microtime(true);
    	}
    	else
    	{
    	    session_start();
    	    $this->redirect('/main/index');
    	    $t2 = microtime(true);
    	}
    	//日志输出
    	file_put_contents("../log/time.log", $start_time."--".$_SERVER['REMOTE_ADDR']."--IndexController/indexAction耗时:".round($t2-$t1,3)."秒"."\r\n",FILE_APPEND);
    }
    
    //backuptable
   /* public function bkAction()
    {  
        $t1=new t1();
        $t1->bktable();
    }
	*/
}
