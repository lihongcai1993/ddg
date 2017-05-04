<?php

namespace Home\Controller;
use Think\Page;
use Think\Verify;
class VipController extends BaseController {

	public function vip()
	{	
		//会员专享商品
		$promtype=M("prom_goods")->where("id =1")->find();
		$goodsarr=M("goods")->where("is_on_sale=1 and prom_type=3 and prom_id=1")->field("goods_name,original_img,goods_name,goods_id,shop_price")->select();


		switch ($promtype['type']) {
			case 0:
			foreach ($goodsarr as $key => $value) {
			$goodsarr[$key]['vip_price']=round($value['shop_price']*$promtype['expression']/100);
			};
				break;
			
			case 1:
				foreach ($goodsarr as $key => $value) {
			$goodsarr[$key]['vip_price']=$value['shop_price']-$promtype['expression'];
			};
				break;

			case 2:
				foreach ($goodsarr as $key => $value) {
			$goodsarr[$key]['vip_price']=$promtype['expression'];
			};
				break;
		}

		$this->assign("goodsarr",$goodsarr);



		
		//一件代发
		$onego=M("prom_goods")->where("id =3")->find();
		$onegoarr=M("goods")->where("is_on_sale=1 and prom_type=3 and prom_id=3")->field("goods_name,original_img,goods_name,goods_id,shop_price")->select();


		switch ($onego['type']) {
			case 0:
			foreach ($onegoarr as $key => $value) {
			$onegoarr[$key]['vip_price']=round($value['shop_price']*$onego['expression']/100);
			};
				break;
			
			case 1:
				foreach ($onegoarr as $key => $value) {
			$onegoarr[$key]['vip_price']=$value['shop_price']-$onego['expression'];
			};
				break;

			case 2:
				foreach ($onegoarr as $key => $value) {
			$onegoarr[$key]['vip_price']=$onego['expression'];
			};
				break;
		}
		// dump($onego);
		// dump($onegoarr);
		$this->assign("onegoarr",$onegoarr);
		
		$this->display();
	
}
}