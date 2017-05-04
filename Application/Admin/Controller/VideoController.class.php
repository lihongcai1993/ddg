<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: 当燃      
 * Date: 2015-09-09
 */

namespace Admin\Controller;


class VideoController extends BaseController {
		public function videolist()
		{
			$where=empty($_GET['sou'])?1:"title like '%$_GET[sou]%'";
			$this->assign("sou",$_GET['sou']);
			$m=M("video");
			$count=$m->where($where)->count();
			$page=new \Think\Page($count,20);
			$video=$m->where($where)->limit("$page->firstRow(),$page->listRows")->select();
			$this->assign("list",$video);
			$this->assign("page",$page->show());
			$this->display();
		}
		public function video()
		{
			$m=M("video");
			if($_POST)
			{	

				if($_POST['old_id'])
				{
					if($_POST['act']!=$_POST['thumb'])
					{
						unlink($_POST['act']);

					}
					$m->data($_POST)->where("id = $_POST[old_id]")->save();
					$this->success("修改成功",U('Video/videolist'));
				}
				else
				{
					$data['title']=$_POST['title'];
					$data['videoname']=$_POST['videoname'];
					$data['link']=$_POST['link'];
					$data['is_open']=$_POST['is_open'];
					$data['act']=$_POST['act'];
					$data['timer']=time();
					$m->data($data)->add();
					$this->success("添加成功",U('Video/videolist'));
				}
			}
			else
			{
				if($_GET['id'])
				{
					$info=$m->where("id= $_GET[id]")->find();
					$this->assign("info",$info);
				}
				
				$this->display();
			}
			
		}
		public function del()
		{
			$id=$_POST['id'];
			$m=M("video");
			$info=$m->find($id);
			if(file_exists("./Public/upload/video".$info['videoname']))
			{
				unlink("./Public/upload/video".$info['videoname']);
			}
			if(file_exists($info['thumb']))
			{
				unlink($info['thumb']);
			}

			$row=$m->where("id =$id")->delete();
			if($row)
			{
				$this->ajaxReturn(1);
			}
			else
			{
				$this->ajaxReturn(0);
			}
		}
}