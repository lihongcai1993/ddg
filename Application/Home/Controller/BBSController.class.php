<?php
namespace Home\Controller;
use Think\Page;

class BBSController extends BaseController {
    public function bbs(){  

    	if(IS_AJAX)
    	{
    		if(empty($_POST['title']))
    		{
    			$this->ajaxReturn("标题不能为空！");
    			die;
    		}
    		else if(empty($_POST['content']))
    		{
    			$this->ajaxReturn("内容不能为空！");
    			die;
    		}
    		else
    		{
    			$m=M("posts");
    			$_POST['timer']=time();
    			$m->create();
    			$m->add();
    			$this->ajaxReturn(1);
    		}
    	}
    	else
    	{
	    		//视频 
	    	$video=M("video")->where("is_open = 1")->order('id desc')->select();  
	    	$this->assign("video",$video);
	        $this->display();
    	}
    	
    }
    public function player()
    {
    	$id=$_GET['id'];
    	$info=M("video")->find($id);
    	$this->assign("info",$info);
    	$this->display();
    }
    
}
    

?>