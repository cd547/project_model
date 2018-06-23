<?php
require_once LIBRARY_PATH . '/Zend/Mail.php';
require_once LIBRARY_PATH . '/Zend/Mail/Transport/Smtp.php';
class mail 
{
    const EMAILADDRESS='18217224172@qq.com';//邮箱地址
    const EMAILNAME='18217224172';//发件名
    const PWD='iyowgzzzzbyheacb';//密码
    const SMTP='smtp.qq.com';//smtp地址
    
    public function message($title,$content,$email)
    {
        $mail = new Zend_Mail("UTF-8");//设置邮件编码
        $config = array(
            'auth'=>'login', 'username'=>self::EMAILNAME,//电子件用户名
            'password'=>self::PWD,
            'ssl'=>"ssl");
        set_time_limit(0);//30秒超时
        $transport = new Zend_Mail_Transport_Smtp(self::SMTP,$config);
        $mail->setDefaultTransport($transport);
         
        $mail->setBodyHtml($content);//可以发送HTML的邮件
        $mail->setFrom(self::EMAILADDRESS, self::EMAILNAME);
        $mail->addTo($email, '');
        $mail->setSubject("=?UTF-8?B?".base64_encode($title)."?=");
        $mail->send();
    }
    
	public function node_finished($project,$node,$email)
	{
	    $mail = new Zend_Mail("UTF-8");//设置邮件编码
	    $config = array(
	        'auth'=>'login', 'username'=>self::EMAILNAME,//电子件用户名
	        'password'=>self::PWD,
	        'ssl'=>"ssl");
	    set_time_limit(0);//30秒超时
	    $transport = new Zend_Mail_Transport_Smtp(self::SMTP,$config);
	    $mail->setDefaultTransport($transport);
	    $mailcontent='项目：'.$project.'的第'.$node.'个节点已经完成';
	   
	    $mail->setBodyHtml($mailcontent);//可以发送HTML的邮件
	    $mail->setFrom(self::EMAILADDRESS, self::EMAILNAME);
	    $mail->addTo($email, $username); $title=$username.'节点信息提醒:项目：'.$project.'的第'.$node.'个节点已经完成';
	    $mail->setSubject("=?UTF-8?B?".base64_encode($title)."?=");
	    $mail->send();
	}
    
	public function problem($username,$helper,$project,$node,$content,$time,$email)
	{

	    $mail = new Zend_Mail("UTF-8");//设置邮件编码
	    $config = array(
	        'auth'=>'login', 'username'=>self::EMAILNAME,//电子件用户名
	        'password'=>self::PWD,
	        'ssl'=>"ssl");
	    set_time_limit(0);//30秒超时
	    $transport = new Zend_Mail_Transport_Smtp(self::SMTP,$config);
	    $mail->setDefaultTransport($transport);
	    $mailcontent=$username.'在项目'.$project.'处'.$node.'节点提出问题如下<br>'.$content.'<br>'.'希望解决的日期是：'.$time.'<br>'.$username;
	   
	    $mail->setBodyHtml($mailcontent);//可以发送HTML的邮件
	    $mail->setFrom(self::EMAILADDRESS, self::EMAILNAME);
	    $mail->addTo($email, $username); $title=$helper.',有问题需要你协助';
	    $mail->setSubject("=?UTF-8?B?".base64_encode($title)."?=");
	    $mail->send();
	 }
	 


}
?>