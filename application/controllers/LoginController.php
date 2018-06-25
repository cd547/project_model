<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/validate.php';
require_once APPLICATION_PATH.'/models/users.php';//model table
class LoginController extends BaseController
{
   //动态获取验证码
   public function ajaximgAction()
   {
       $valimg=new validate();
       $url=$valimg->imgAction();
       $this->view->info=$url;
       $this->render('ajax');
   }
   //ajax login
   public function ajaxloginAction()
   {
       $t1 = microtime(true);
       date_default_timezone_set('PRC'); //设置中国时区
       $time = time();
       $arr=array();
       $starttime = date("y-m-d H:i:s",$time); //2010-08-29;
       //获取前台数据
       $userData['username']=$this->getRequest()->getParam("username","");
       $userData['password']=$this->getRequest()->getParam("password","");
        $user=new users();
        $loginuser=$user->getUserbyusername($userData['username']);
        if(count($loginuser)==1)//finded
        {
            if( $loginuser[0]['password']===md5($userData['password']))
            {
                //ok,get user info ,save into session
                if (!session_id())
                    session_start();
                    $_SESSION['loginuser']=$loginuser[0];
                    $user->loginTime($loginuser[0]['id']);
                    $arr[0]['vali']="ok";
                  //  $this->_redirect('/main/main');  
            }
            else
            {
                $arr[0]['vali']="密码错误"; 
            }
        }
        else
        {
            $arr[0]['vali']="用户名错误";
        }
        $info=json_encode($arr);
        $this->view->info=$info;
        $this->render('ajax');
        $t2 = microtime(true);
       file_put_contents("../log/time.log", $starttime."--".$loginuser[0]['username']."--".$_SERVER['REMOTE_ADDR']."--LoginController/ajaxlogin耗时:".round($t2-$t1,3)."秒"."\r\n",FILE_APPEND);
        //发送短信
        /*
        if( $arr[0]['vali']=="ok")
        {
            if($user->receive_message($loginuser[0]['id']))
            {
                $message=new message();
                $message->send($loginuser[0]['cellphone'],$loginuser[0]['username'],$starttime);
            }
            file_put_contents("c:/time.log", $starttime."--".$loginuser[0]['username']."--".$_SERVER['REMOTE_ADDR']."--LoginController/ajaxlogin耗时:".round($t2-$t1,3)."秒"."\r\n",FILE_APPEND);
        }
        */
   }
   //注销
   public function logoutAction()
   {
       $t1 = microtime(true);
       date_default_timezone_set('PRC'); //设置中国时区
       $time = time();
       $starttime = date("y-m-d H:i:s",$time);
       //清空session
       if (!session_id())
           session_start();
           $exeuser=$_SESSION['loginuser']['username'];
           session_destroy();
           $t2 = microtime(true);
       file_put_contents("../log/time.log", $starttime."--".$exeuser."--".$_SERVER['REMOTE_ADDR']."--LoginController/logoutAction耗时:".round($t2-$t1,3)."秒"."\r\n",FILE_APPEND);
        //$this->forward('index','index');
       $this->redirect('/index/index');
   }

}

