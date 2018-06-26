<?php
class validata
{
    public function imgAction()
    {
    
        //$this->codeSession = new Zend_Session_Namespace('code'); //在默认构造函数里实例化
        $captcha = new Zend_Captcha_Image(array('font'=>'vendors/arial.ttf', //字体文件路径
            'fontsize'=>18, //字号
            'imgdir'=>'captcha/', //验证码图片存放位置
            // 'session'=>  $this->codeSession, //验证码session值
            'width'=>120, //图片宽
            'height'=>32,   //图片高
            'wordlen'=>5 )); //字母数
        $captcha->setLineNoiseLevel(1);//设置线干扰级别(默认5)
        $captcha->setDotNoiseLevel(10);//设置点干扰级别(默认100)
        $captcha->setGcFreq(10); //设置删除生成的旧的验证码图片的随机几率(默认10)
        $id= $captcha->generate(); //生成图片
        //$this->view->ImgDir = $captcha->getImgDir();
       // $this->view->captchaId = $captcha->getId(); //获取文件名，md5编码
        //$this->codeSession->code=$captcha->getWord(); //获取当前生成的验证字符串
        // $p = $this->view->ImgDir . $captcha->getId() . $captcha->getSuffix();
        //$captcha
        //  echo $this->codeSession->code;
        // echo "<img src='/$p' />" ;
        $url=$captcha->getId();
        $codeSession = new Zend_Session_Namespace('captcha_code_' . $id);
        $codeSession->code = $captcha->getWord();
        file_put_contents("../log/url.log", $id."  code：". $codeSession->code."\r\n",FILE_APPEND);
        return $url;
        //exit;
    
    }
    

}
