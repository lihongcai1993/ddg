<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */ 
namespace Home\Controller;
use Think\Page;

class GroupBuyController extends BaseController {
    public function index(){
        //团购
        $row=M("group_buy")->select();
        foreach ($row as $k => $v) {
            $row[$k]['goods_info'] = M("goods")->where("is_on_sale=1 and prom_type=2 and prom_id = $v[id]")->join('tp_goods_images ON tp_goods_images.goods_id = tp_goods.goods_id',"LEFT")->select();//团购商品
            $c=M("ad")->where("pid = $v[img_id]")->find();
            $row[$k]['guanggao']=$c['ad_code'];
        }
        $this->assign("row",$row);
        
        //限时抢购
        $timer=time();
        $panicbuy=M("flash_sale")->where("start_time<$timer")->order("id desc")->limit(14)->select();
        
        foreach ($panicbuy as $key => $value) {
            $panicbuy[$key]['goods_info']= M("goods")->where("goods_id=$value[goods_id]")->find();
            $panicbuy[$key]['guanggao']=M("ad")->where("pid = $value[img_id]")->find();
        }
        $this->assign("panicbuy",$panicbuy);


        //热卖好货
        $goodstype=M("goods_type")->where("hotshow=1")->order("id desc")->select();
       
        $hotgoods=M("goods")->where("is_on_sale=1 and goods_type = ".$goodstype[0][id]." and is_hot=1")->field("goods_name,original_img,goods_id")->limit(6)->select();
        foreach($hotgoods as $hk => $hv)
        {
            $hotgoods[$hk]['goods_name']=strlength($hv['goods_name'],10);
        }

        //获取随机商品
        $randgoods=M();
        $randgoodsarr=$randgoods->query("SELECT goods_name,original_img,goods_id,shop_price FROM tp_goods WHERE goods_id >= ((SELECT MAX(goods_id) FROM tp_goods)-(SELECT MIN(goods_id) FROM tp_goods)) * RAND() + (SELECT MIN(goods_id) FROM tp_goods) and is_hot=1 and is_on_sale=1  LIMIT 4;");
        foreach($randgoodsarr as $rk => $rv)
        {
            $randgoodsarr[$rk]['goods_name']=strlength($rv['goods_name'],10);
        }

        $this->assign("randgoodsarr",$randgoodsarr);
        $this->assign("hotgoods",$hotgoods);

        $this->assign("goodstype",$goodstype);
        $this->display();
    }
    function showgoodsajax()
    {
        $hotgoods=M("goods")->where("is_on_sale=1 and goods_type = $_POST[typeid] and is_hot=1")->field("goods_name,original_img,goods_id")->limit(6)->select();
        foreach($hotgoods as $hk => $hv)
        {
            $hotgoods[$hk]['goods_name']=strlength($hv['goods_name'],10);
        }
        $this->ajaxReturn($hotgoods);
        //print_r($hotgoods);
    }
    function shwogoodsrand()
    {
         //获取随机商品
        $randgoods=M();
        $randgoodsarr=$randgoods->query("SELECT goods_name,original_img,goods_id,shop_price FROM tp_goods WHERE goods_id >= ((SELECT MAX(goods_id) FROM tp_goods)-(SELECT MIN(goods_id) FROM tp_goods)) * RAND() + (SELECT MIN(goods_id) FROM tp_goods) and is_hot=1 and is_on_sale=1  LIMIT 4;");
        foreach($randgoodsarr as $rk => $rv)
        {
            $randgoodsarr[$rk]['goods_name']=strlength($rv['goods_name'],10);
        }
        $this->ajaxReturn($randgoodsarr);

    }
   /**
    * 商品详情页
    */ 
    public function goodsInfo(){
        //  form表单提交
        C('TOKEN_ON',true);        
        
        $goodsLogic = new \Home\Logic\GoodsLogic();
        $goods_id = I("get.id");
        
        $group_buy_info = M('GroupBuy')->where("goods_id = $goods_id and ".time()." >= start_time and ".time()." <= end_time ")->find(); // 找出这个商品
        if(empty($group_buy_info)) 
        {
            $this->success("此商品没有团购活动",U('Home/Goods/goodsInfo',array('id'=>$goods_id)));
            exit;
        }
                    
        $goods = M('Goods')->where("goods_id = $goods_id")->find();
        $goods_images_list = M('GoodsImages')->where("goods_id = $goods_id")->select(); // 商品 图册
                
        $goods_attribute = M('GoodsAttribute')->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M('GoodsAttr')->where("goods_id = $goods_id")->select(); // 查询商品属性表
                        
        $Model = new \Think\Model();        
        // 商品规格 价钱 库存表 找出 所有 规格项id
        $keys = M('SpecGoodsPrice')->where("goods_id = $goods_id")->getField("GROUP_CONCAT(`key` SEPARATOR '_') ");         
        if($keys)
        {
             $specImage =  M('SpecImage')->where("goods_id = $goods_id and src != '' ")->getField("spec_image_id,src");// 规格对应的 图片表， 例如颜色                
             $keys = str_replace('_',',',$keys);             
             $sql  = "SELECT a.name,a.order,b.* FROM __PREFIX__spec AS a INNER JOIN __PREFIX__spec_item AS b ON a.id = b.spec_id WHERE b.id IN($keys) ORDER BY a.order";
             $filter_spec2 = $Model->query($sql);             
             foreach($filter_spec2 as $key => $val)
             {                                  
                 $filter_spec[$val['name']][] = array(
                     'item_id'=> $val['id'],
                     'item'=> $val['item'],
                     'src'=>$specImage[$val['id']],
                     );                 
             }            
        }                
        $spec_goods_price  = M('spec_goods_price')->where("goods_id = $goods_id")->getField("key,price,store_count"); // 规格 对应 价格 库存表
        M('Goods')->where("goods_id=$goods_id")->save(array('click_count'=>$goods['click_count']+1 )); // 统计点击数
        $commentStatistics = $goodsLogic->commentStatistics($goods_id);// 获取某个商品的评论统计
        $navigate_goods = navigate_goods($goods_id,1); // 面包屑导航        
                
        $this->assign('group_buy_info',$group_buy_info);
        $this->assign('spec_goods_price', json_encode($spec_goods_price,true)); // 规格 对应 价格 库存表
        $this->assign('navigate_goods',$navigate_goods);
        $this->assign('commentStatistics',$commentStatistics);
        $this->assign('goods_attribute',$goods_attribute);       
        $this->assign('goods_attr_list',$goods_attr_list);
        $this->assign('filter_spec',$filter_spec);
        $this->assign('goods_images_list',$goods_images_list);
        $this->assign('goods',$goods);
        $this->display();
    } 
    
    
    /**
     * 团购活动列表
     */
    public function GroupBuyList()
    {
        $list = M('GroupBuy')->where(time()." >= start_time and ".time()." <= end_time ")->select(); // 找出这个商品        
        $this->assign('list', $list);
        $this->display();
    }
}