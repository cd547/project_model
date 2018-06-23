<?php
class weichat
{
	/** 微信文本消息提醒
	 * @param  $on 微信发送功能开启
	 * @param  $touser 微信发送对象 （账号）
	 * @param  $content 微信发送内容
	 */
	 const APPID="wx80fc258b43434cb2";//微信IDwx80fc258b43434cb2 
	 const APPSECRET="Yng2kv1Ihm_cNyQ6_g_K6rxk1wCJ9gEaxdnwMlAESx0";

	
	 //"5kMtzb20IjhTiVYX7qIKDrEth-M2AbmShZmc6LRPf_nW2AV4WpGQbW5QArSEmIQg";//secret

	public function send_text_touser($on,$touser,$content)
	{
		$TOKEN_URL="https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".self::APPID."&corpsecret=".self::APPSECRET;
		if($on)
		{
			//$json=file_get_contents($TOKEN_URL);
			//弃用上面的方法，CURL效率高于file_get_contents
			$t1 = microtime(true);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_URL, $TOKEN_URL);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
			$json= curl_exec($curl);
			//$array=curl_getinfo($curl);
			//file_put_contents("c:/curlinfo.log",var_export($array, true),FILE_APPEND);		
			if (curl_errno($curl)) {
				file_put_contents("../log/curlerr.log", "readerr:".curl_error($curl)."\r\n",FILE_APPEND);
				return 'Errno'.curl_error($curl);
			}
			curl_close($curl);
			$t2 = microtime(true);
			file_put_contents("../log/curltime.log", "获取耗时:".round($t2-$t1,3)."秒"."\r\n",FILE_APPEND);
			$result=json_decode($json);
			$ACC_TOKEN=$result->access_token;
			//echo  $ACC_TOKEN;
			$url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=$ACC_TOKEN";
			$data ='{
       	 		"touser":"'.$touser.'",
  			 	"msgtype": "text",
   				"agentid": "1000002",
   				"text": {"content": "'.$content.'"},
   				"safe":"0"
    			}';
			$t3 = microtime(true);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$result1= curl_exec($curl);
			if (curl_errno($curl)) {
				file_put_contents("../log/curlerr.log", "senderr:".curl_error($curl)."\r\n",FILE_APPEND);
				return 'Errno'.curl_error($curl);
			}
			curl_close($curl);
			$t4 = microtime(true);
			file_put_contents("../log/curltime.log", "发送耗时:".round($t4-$t3,3)."秒"."\r\n",FILE_APPEND);
			file_put_contents("../log/result1.log", $result1."\r\n",FILE_APPEND);
			return $result1;
		}
	}
	//获取用户信息
	public function getuserinfo($code)
	{
		//获取access_token
		$TOKEN_URL="https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".self::APPID."&corpsecret=".self::APPSECRET;
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_URL, $TOKEN_URL);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
			$json= curl_exec($curl);
			//$array=curl_getinfo($curl);
			//file_put_contents("c:/curlinfo.log",var_export($array, true),FILE_APPEND);		
			if (curl_errno($curl)) {
				file_put_contents("../log/curlerr.log", "readerr:".curl_error($curl)."\r\n",FILE_APPEND);
				return 'Errno'.curl_error($curl);
			}
			curl_close($curl);
			$result=json_decode($json);
			$ACC_TOKEN=$result->access_token;
			//echo  $ACC_TOKEN;
			//获取用户Userid
			$url = 
"https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$ACC_TOKEN&code=$code&agentid=".self::APPID;

			//"https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::APPID."&secret=".self::APPSECRET."&code=".$code."&grant_type=authorization_code";
			$curl1 = curl_init();
			$this_header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");

			curl_setopt($curl1,CURLOPT_HTTPHEADER,$this_header);
			curl_setopt($curl1, CURLOPT_URL, 'http://');

			curl_setopt($curl1, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl1, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl1,CURLOPT_URL,$url); 
			//curl_setopt($curl1,CURLOPT_HEADER,0); 
			curl_setopt($curl1, CURLOPT_RETURNTRANSFER, 1 ); 
			//curl_setopt($curl1, CURLOPT_CONNECTTIMEOUT, 10);

			$json1= curl_exec($curl1);
			if (curl_errno($curl1)) {
				file_put_contents("../log/curlerr.log", "senderr:".curl_error($curl1)."\r\n",FILE_APPEND);
				return 'Errno'.curl_error($curl1);
			}
			curl_close($curl1);
			$result1=json_decode($json1,true);
			//$openid = $result1['openid']; 
			//file_put_contents("c:/weeeeee.log", "Now:".$result1['UserId']."\r\n",FILE_APPEND);
			return $result1['UserId'];
			
	}
}