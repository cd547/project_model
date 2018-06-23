<?php
require_once 'weichat.php';
require_once 'users.php';
class message
{
    //修改为您的apikey. apikey可在官网（https://www.dingdongcloud.com)登录后获取
	 const APIKEY="bac054af437d623dd84d7cd278648d21";//
	
    //通知
    function send_tz($ch,$data){
        curl_setopt ($ch, CURLOPT_URL, 'https://api.dingdongcloud.com/v1/sms/sendtz');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($ch);
    }
    // 发送通知短信
    // 修改为您要发送的短信内容,需要对content进行编码
    public function send($mobile,$username,$time)
    {
        $ch = curl_init();  
        /* 设置验证方式 */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
        /* 设置返回结果为流 */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /* 设置超时时间*/
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        /* 设置通信方式 */
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $tzcontent="【上海开沙信息科技有限公司】".$username."您好，您于".$time."登录我们网站。退订回T";
        $data=array('content'=>urlencode($tzcontent),'apikey'=>self::APIKEY,'mobile'=>$mobile);
        $json_data = $this->send_tz($ch,$data);
        $array = json_decode($json_data,true);
        //echo '<pre>';print_r($array);
        curl_close($ch);
    }
    
    public function sendv2($mobile,$content)
    {

       // $ch = curl_init();  
        /* 设置验证方式 */
      //  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
        /* 设置返回结果为流 */
      //  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /* 设置超时时间*/
      //  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        /* 设置通信方式 */
      //  curl_setopt($ch, CURLOPT_POST, 1);
       // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       // $tzcontent="【上海开沙信息科技有限公司】".$content."退订回T";
       // $data=array('content'=>urlencode($tzcontent),'apikey'=>self::APIKEY,'mobile'=>$mobile);
       // $json_data = $this->send_tz($ch,$data);
       // $array = json_decode($json_data,true);
        //echo '<pre>';print_r($array);
       // curl_close($ch);

        $weichart=new weichat();
        
        $user=new users();
        
        $wxuserid=$user->getweichartbycellphone($mobile);
        $weichart->send_text_touser(true, $wxuserid, "【陈家镇建设项目平台】 ".$content);
        
		return 1;
    }
    
    
    
    //weichart
    public function sendv3($usercode,$content)
    {
        $weichart=new weichat();
        //查找用户
       // $weichart->getuserinfo('wsq');
       $user=new users();
       $wxuserid=$user->getweichartbycode($usercode);
        $weichart->send_text_touser(true, $wxuserid, "【陈家镇建设项目平台】 ".$content);
    }
    
    public function sendv4($mobile,$content)
    {
    
        $weichart=new weichat();
    
        $user=new users();
    
        $wxuserid=$user->getweichartbycellphone($mobile);
        $r=$weichart->send_text_touser(true, $wxuserid, "【陈家镇建设项目平台】 ".$content);
       
        return  $r;
    }
}