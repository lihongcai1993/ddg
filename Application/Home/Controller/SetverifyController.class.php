<?php

namespace Home\Controller;
use Home\Logic\UsersLogic;
use Think\Verify;

class SetverifyController extends BaseController {

	 //生成验证码的控制器
	public function setverify()
	{
		//ob_clean();//用来丢弃输出缓冲区中的内容，如果你的网站有许多生成的图片类文件，那么想要访问正确，就要经常清除缓冲区

        $Verify = new \Think\Verify(); 
            
        $Verify->entry(); 
	} 

}
 



