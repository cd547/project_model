<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/validate.php';
require_once APPLICATION_PATH.'/models/users.php';//model table
require_once APPLICATION_PATH.'/models/message.php';//model table
class MessageController extends BaseController
{
    //messageAPI
    public function msgAction()
    {
        $mobile=$this->getRequest()->getParam("mobile","");
        $content=$this->getRequest()->getParam("content","");
        file_put_contents("c:/ddd.log",'mobile->'.$mobile.' content'.$content."\r\n",FILE_APPEND);
    	$message=new message();
    	$r=$message->sendv4($mobile, $content);
    	file_put_contents("c:/msg547.log",($r)."\r\n",FILE_APPEND);
    	$this->view->res=$r;
    	$this->render('msg');
    }
  

}

