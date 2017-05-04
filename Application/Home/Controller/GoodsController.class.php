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
use Home\Logic\CartLogic;
use Home\Logic\GoodsLogic;
use Think\AjaxPage;
use Think\Page;
use Think\Verify;
class GoodsController extends BaseController {
    public function index(){      
        $this->display();
    }


   /**
    * 商品详情页
    */ 
   

   public function remove(){
    if (IS_AJAX) {
        $id=$_POST['id'];
        $pid=$_POST['pid'];
        unset($_SESSION['spec_goods_arr'][$pid]);
        // $_SESSION['spec_goods_arr'][$id]=null;
    }
   }
    public function goodsInfo(){

        if (IS_AJAX) {
            $id=$_POST['id'];
            $pid=$_POST['pid'];
            $_SESSION['spec_goods_arr'][$pid]=$id;
            if(count($_SESSION['spec_goods_arr'])==$_SESSION['spec_goods_length'])
            {
                asort($_SESSION['spec_goods_arr']);
                $ar=implode('_',$_SESSION['spec_goods_arr']);
                foreach ($_SESSION['spec_goods_price_session'] as $key => $value) {
                    if($ar==$value['key'])
                    {
                        $price=$value['price'];
                    }
                }
                $re=array(
                    'status' => 1,
                    'msg'   =>  $price, 
                    );
                $this->ajaxReturn($re);
              
            }
            else
            {
                $this->ajaxReturn(0);
            }
            exit;
        }  
         
        unset($_SESSION['spec_goods_arr']);
        $goodsLogic = new GoodsLogic();
        $goods_id = $_GET['id'];
        $goods = M('Goods')->where("goods_id = $goods_id")->find();

        if(empty($goods) || ($goods['is_on_sale'] == 0)){
        	$this->error('该商品已经下架');
        }
        if($goods['brand_id']){
            $brnad = M('brand')->where("id =".$goods['brand_id'])->find();
            $goods['brand_name'] = $brnad['name'];
        }
        
        $goods_images_list = M('GoodsImages')->where("goods_id = $goods_id")->select(); // 商品 图册        
        $goods_attribute = M('GoodsAttribute')->where("type_id=$goods[goods_type]")->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M('GoodsAttr')->where("goods_id = $goods_id")->select(); // 查询商品属性表                        
        foreach ($goods_attribute as $key => $value) {
            foreach ($goods_attr_list as $k => $v) {
                if($key==$v['attr_id'])
                {
                    $goodsattr[$key]['value']=$v['attr_value'];
                    $goodsattr[$key]['name']=$value;
                    break;
                }
                else
                {
                    $goodsattr[$key]['value']="";
                    $goodsattr[$key]['name']=$value;
                }
            }
        }
        //商品是否正在限时抢购中        
        if($goods['prom_type'] == 1)
        {
            $goods['flash_sale'] = get_goods_promotion($goods['goods_id']);                        
            $flash_sale = M('flash_sale')->where("id = {$goods['prom_id']}")->find();
            $this->assign('flash_sale',$flash_sale);
        }
        // 商品是否正在团购中        
        if($goods['prom_type'] == 2)
        {
            $group_sale = M('group_buy')->where("id = $goods[prom_id]")->find();
            $this->assign('group_sale',$group_sale);
        }
         // 商品是高级会员专享或一件代发或其他活动        
        if($goods['prom_type'] == 3)
        {
            $goods['flash_sale'] = get_goods_promotion($goods['goods_id']);                        
            $flash_sale = M('flash_sale')->where("id = {$goods['prom_id']}")->find();
            //$this->assign('flash_sale',$flash_sale);
        }

        $spec=M("spec")->where("type_id = $goods[goods_type]")->select();

        $spec_goods_price  = M('spec_goods_price')->where("goods_id = $goods_id")->getField("key,key_name,price,store_count"); // 规格 对应 价格 库存表

        $_SESSION['spec_goods_price_session']=$spec_goods_price;
        if($spec_goods_price)
        {
            $goods_spec=$goodsLogic->findgoodspec($spec_goods_price,$goods_id);
        }

        $_SESSION['spec_goods_length']=count($goods_spec);
     

        M('Goods')->where("goods_id=$goods_id")->save(array('click_count'=>$goods['click_count']+1 )); //统计点击数
        //$commentStatistics = $goodsLogic->commentStatistics($goods_id);// 获取某个商品的评论统计
        //$point_rate = tpCache('shopping.point_rate');
        $this->assign('spec_goods_price', json_encode($spec_goods_price,true)); // 规格 对应 价格 库存表
        //$this->assign('navigate_goods',navigate_goods($goods_id,1));// 面包屑导航 
        //$this->assign('commentStatistics',$commentStatistics);//评论概览
        $this->assign('goodsattr',$goodsattr);//属性值     
        $this->assign('goods_spec',$goods_spec);//规格参数
        $this->assign('goods_images_list',$goods_images_list);//商品缩略图
        //$this->assign('siblings_cate',$goodsLogic->get_siblings_cate($goods['cat_id']));//相关分类
        //$this->assign('look_see',$goodsLogic->get_look_see($goods));//看了又看      
        $this->assign('goods',$goods);
        //$this->assign('point_rate',$point_rate);
        $this->display();
        
    }   



    /**
     * 加入购物车
     */
    public function add_cart()
    {    

        if(!preg_match("/^[1-9][0-9]*$/",$_POST['num'])){
                $this->ajaxReturn("商品数量非法");
            }
        if($_SESSION['spec_goods_length']!=0)
        {
            if(count($_SESSION['spec_goods_arr'])!=$_SESSION['spec_goods_length'])
            {
                $this->ajaxReturn("请选择商品规格");
            }
            $spec_arr=$_SESSION['spec_goods_arr'];
            $spec_str=implode("_",$spec_arr);
            foreach ($_SESSION['spec_goods_price_session'] as $key => $value) {
                    if($spec_str==$value['key'])
                    {
                        $spec_info=$value;
                    }
            } 
            if($_POST['num']>=$spec_info['store_count'])
            {
                $this->ajaxReturn("商品数量超过库存量");
            }
        }
            
            
            // print_r($spec_str);
            //print_r($spec_info);
           
            $row=M("goods")->where("goods_id = $_POST[goodsid]")->field('goods_id,goods_sn,goods_name,market_price,shop_price,prom_type,prom_id,is_on_sale,store_count')->find();
            if(!$row || $row['is_on_sale']!=1)
            {
                $this->ajaxReturn("商品不存在或已被下架");
            }
            if($row['store_count']<=$_POST['num'])
            {
                $this->ajaxReturn("商品数量超过库存量");
            }
            $data=array(
                'goods_id'     => $row['goods_id'],
                'goods_sn'     => $row['goods_sn'],
                'goods_name'   => $row['goods_name'],
                'market_price' => $row['market_price'],
                'goods_price'  => $row['shop_price'],
                'prom_id'      => $row['prom_id'],
                'prom_type'    => $row['prom_type'],
                'goods_num'    => $_POST['num'],
                'spec_key'     => $spec_info['key'],
                'spec_key_name'=> $spec_info['key_name'],
                'selected'     => 1,
                'add_time'     => time(),
                'user_id'      => $_SESSION['user']['user_id'],
                );
            $m=M("cart");
            $have=$m->where("user_id =$data[user_id] and goods_id= $data[goods_id]")->find();
            if($have)
            {
                $this->ajaxReturn("已加入,请勿重复添加");
            }
            else
            {
                $msg=$m->data($data)->add();
                if($msg)
                {
                    $this->ajaxReturn("加入购物车成功");
                }
                else
                {
                    $this->ajaxReturn("加入购物车失败");
                }
            }
            
            
       
    }



    /**
     * 获取可发货地址
     */
    public function getRegion()
    {
        $goodsLogic = new GoodsLogic();
        $region_list = $goodsLogic->getRegionList();//获取配送地址列表
        $region_list['status'] = 1;
        $this->ajaxReturn($region_list);
    }
    
    /**
     * 商品列表页
     */
    public function goodsList(){

        //导航栏类别
        $row=M("goods_category")->where("is_show = 1 and parent_id=0")->order("sort_order desc")->select();
        foreach($row as $k=>$v)
        {
            $res[$k]=M("goods_category")->where("parent_id =$v[id]")->select();
            $row[$k]['sontype']=$res[$k];
            foreach ($res[$k] as $key => $value) {
                $row[$k]['goods'][]=M("goods")->where("is_on_sale =1  and cat_id= $value[id]")->order("sort desc")->find();
                
            }
            
        }
       



        $this->assign("row",$row);
        $this->assign("res",$res);
        
        //dump($row);
        $this->display();



    }    
    public function goodsListSecond(){

        //按类别查询商品
        $type_id=$_GET['id'];
        $t= M();
        $type_name=$t->query("select * from tp_goods_category  where id = $type_id or id=(select parent_id from tp_goods_category where id=$type_id)");
        $this->assign("type_name",$type_name);
        //dump($type_name);
        $m=M("goods");
        $count=$m->where("cat_id=$type_id")->count();
        $page=new \Think\Page($count,1);
        $goodsarr=$m->where("cat_id= $type_id")->limit("$page->firstRow,$page->listRows")->field("goods_name,goods_id,original_img,market_price,shop_price")->select();
        
        $this->assign("goodsarr",$goodsarr);
        $this->assign("page",$page->show());


        //导航栏类别
        $row=M("goods_category")->where("is_show = 1 and parent_id=0")->order("sort_order desc")->select();
        foreach($row as $k=>$v)
        {
            $res[$k]=M("goods_category")->where("parent_id =$v[id]")->select();
            $row[$k]['sontype']=$res[$k];
          
            
        }
        $this->assign("row",$row);
        $this->assign("res",$res);



        
        $this->display();



    }    

    /**
     *  查询配送地址，并执行回调函数
     */
    public function region()
    {
        $fid = I('fid');
        $callback = I('callback');
        $parent_region = M('region')->field('id,name')->where(array('parent_id'=>$fid))->select();
        echo $callback.'('.json_encode($parent_region).')';
        exit;
    }

    /**
     * 商品物流配送和运费
     */
    public function dispatching()
    {
        $goods_id = I('goods_id');//143
        $region_id = I('region_id');//28242
        $goods_logic = new GoodsLogic();
        $dispatching_data = $goods_logic->getGoodsDispatching($goods_id,$region_id);
        $this->ajaxReturn($dispatching_data);
    }

    /**
     * 商品搜索列表页
     */
    public function search()
    {
       //C('URL_MODEL',0);
        $filter_param = array(); // 帅选数组                        
        $id = I('get.id',0); // 当前分类id 
        $brand_id = I('brand_id',0);         
        $sort = I('sort','goods_id'); // 排序
        $sort_asc = I('sort_asc','asc'); // 排序
        $price = I('price',''); // 价钱
        $start_price = trim(I('start_price','0')); // 输入框价钱
        $end_price = trim(I('end_price','0')); // 输入框价钱
        if($start_price && $end_price) $price = $start_price.'-'.$end_price; // 如果输入框有价钱 则使用输入框的价钱
        $q = urldecode(trim(I('q',''))); // 关键字搜索
        empty($q) && $this->error('请输入搜索词');


        $id && ($filter_param['id'] = $id); //加入帅选条件中                       
        $brand_id  && ($filter_param['brand_id'] = $brand_id); //加入帅选条件中        
        $price  && ($filter_param['price'] = $price); //加入帅选条件中
        $q  && ($_GET['q'] = $filter_param['q'] = $q); //加入帅选条件中
        
        $goodsLogic = new \Home\Logic\GoodsLogic(); // 前台商品操作逻辑类
               
        $where  = array(
            'is_on_sale' => 1
        );
        //引入
        if(file_exists(PLUGIN_PATH.'coreseek/sphinxapi.php'))
        {
            require_once("plugins/coreseek/sphinxapi.php");
            $cl = new \SphinxClient();
            $cl->SetServer(C('SPHINX_HOST'), C('SPHINX_PORT'));
            $cl->SetConnectTimeout(10);
            $cl->SetArrayResult(true);
            $cl->SetMatchMode(SPH_MATCH_ANY);
            $res = $cl->Query($q, "*");
            if($res){
                $res = $cl->Query($q, "*");
                $goods_id_array = array();
                foreach ($res['matches'] as $key => $value) {
                    $goods_id_array[] = $value['id'];
                }
                if(!empty($goods_id_array)){
                    $where['goods_id'] = array('in',$goods_id_array);
                }else{
                    $where['goods_id'] = 0;
                }
            }else{
                $where['goods_name'] = array('like','%'.$q.'%');
            }
        }else{
            $where['goods_name'] = array('like','%'.$q.'%');
        }


        if($id)
        {
            $cat_id_arr = getCatGrandson ($id);
            $where['cat_id'] = array('in',implode(',', $cat_id_arr));
        }
        
        $search_goods = M('goods')->where($where)->getField('goods_id,cat_id');
        $filter_goods_id = array_keys($search_goods);
        $filter_cat_id = array_unique($search_goods); // 分类需要去重
        if($filter_cat_id)        
        {
            $cateArr = M('goods_category')->where("id in(".implode(',', $filter_cat_id).")")->select();            
            $tmp = $filter_param;
            foreach($cateArr as $k => $v)            
            {
                $tmp['id'] = $v['id'];
                $cateArr[$k]['href'] = U("/Home/Goods/search",$tmp);                            
            }                
        }                        
        // 过滤帅选的结果集里面找商品        
        if($brand_id || $price)// 品牌或者价格
        {
            $goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id,$price); // 根据 品牌 或者 价格范围 查找所有商品id    
            $filter_goods_id = array_intersect($filter_goods_id,$goods_id_1); // 获取多个帅选条件的结果 的交集
        }
        
        $filter_menu  = $goodsLogic->get_filter_menu($filter_param,'search'); // 获取显示的帅选菜单
        $filter_price = $goodsLogic->get_filter_price($filter_goods_id,$filter_param,'search'); // 帅选的价格期间         
        $filter_brand = $goodsLogic->get_filter_brand($filter_goods_id,$filter_param,'search',1); // 获取指定分类下的帅选品牌        
                                
        $count = count($filter_goods_id);
        $page = new Page($count,20);
        if($count > 0)
        {
            $goods_list = M('goods')->where("is_on_sale=1 and goods_id in (".  implode(',', $filter_goods_id).")")->order("$sort $sort_asc")->limit($page->firstRow.','.$page->listRows)->select();
            $filter_goods_id2 = get_arr_column($goods_list, 'goods_id');
            if($filter_goods_id2)
            $goods_images = M('goods_images')->where("goods_id in (".  implode(',', $filter_goods_id2).")")->select();       
        }    
                
        $this->assign('goods_list',$goods_list);  
        $this->assign('goods_images',$goods_images);  // 相册图片
        $this->assign('filter_menu',$filter_menu);  // 帅选菜单
        $this->assign('filter_brand',$filter_brand);  // 列表页帅选属性 - 商品品牌
        $this->assign('filter_price',$filter_price);// 帅选的价格期间
        $this->assign('cateArr',$cateArr);
        $this->assign('filter_param',$filter_param); // 帅选条件
        $this->assign('cat_id',$id);
        $this->assign('page',$page);// 赋值分页输出
        $this->assign('q',I('q'));
        C('TOKEN_ON',false);
        $this->display();
    }
    
    /**
     * 商品咨询ajax分页
     */
    public function ajax_consult(){        
        $goods_id = I("goods_id",'0');        
        $consult_type = I('consult_type','0'); // 0全部咨询  1 商品咨询 2 支付咨询 3 配送 4 售后
                 
        $where = " parent_id = 0 and goods_id = $goods_id";
        if($consult_type > 0)
            $where .= " and consult_type = $consult_type ";
        
        $count = M('GoodsConsult')->where($where)->count();        
        $page = new AjaxPage($count,5);
        $show = $page->show();        
        $list = M('GoodsConsult')->where($where)->order("id desc")->limit($page->firstRow.','.$page->listRows)->select();
        $replyList = M('GoodsConsult')->where("parent_id > 0")->order("id desc")->select();
        
        $this->assign('consultCount',$count);// 商品咨询数量
        $this->assign('consultList',$list);// 商品咨询
        $this->assign('replyList',$replyList); // 管理员回复
        $this->assign('page',$show);// 赋值分页输出        
        $this->display();        
    }
    
    /**
     * 商品评论ajax分页
     */
    public function ajaxComment(){        
        $goods_id = I("goods_id",'0');        
        $commentType = I('commentType','1'); // 1 全部 2好评 3 中评 4差评
        if($commentType==5){
        	$where = "is_show = 1 and  goods_id = $goods_id and parent_id = 0 and img !='' ";
        }else{
        	$typeArr = array('1'=>'0,1,2,3,4,5','2'=>'4,5','3'=>'3','4'=>'0,1,2');
        	$where = "is_show = 1 and  goods_id = $goods_id and parent_id = 0 and ceil((deliver_rank + goods_rank + service_rank) / 3) in($typeArr[$commentType])";
        }
        $count = M('Comment')->where($where)->count();                
        
        $page = new AjaxPage($count,5);
        $show = $page->show();        
        $list = M('Comment')->where($where)->order("add_time desc")->limit($page->firstRow.','.$page->listRows)->select();
        $replyList = M('Comment')->where("is_show = 1 and  goods_id = $goods_id and parent_id > 0")->order("add_time desc")->select();
        
        foreach($list as $k => $v){
            $list[$k]['img'] = unserialize($v['img']); // 晒单图片            
        }        
        $this->assign('commentlist',$list);// 商品评论
        $this->assign('replyList',$replyList); // 管理员回复
        $this->assign('page',$show);// 赋值分页输出        
        $this->display();        
    }    
    
    /**
     *  商品咨询
     */
    public function goodsConsult(){
        //  form表单提交
        C('TOKEN_ON',true);        
        $goods_id = I("goods_id",'0'); // 商品id
        $consult_type = I("consult_type",'1'); // 商品咨询类型
        $username = I("username",'TPshop用户'); // 网友咨询
        $content = I("content"); // 咨询内容
        
        $verify = new Verify();
        if (!$verify->check(I('post.verify_code'),'consult')) {
            $this->error("验证码错误");
        }
        
        $goodsConsult = M('goodsConsult');        
        if (!$goodsConsult->autoCheckToken($_POST))
        {            
                $this->error('你已经提交过了!', U('/Home/Goods/goodsInfo',array('id'=>$goods_id))); 
                exit;
        }
        
        $data = array(
            'goods_id'=>$goods_id,
            'consult_type'=>$consult_type,
            'username'=>$username,
            'content'=>$content,
            'add_time'=>time(),
        );        
        $goodsConsult->add($data);        
        $this->success('咨询已提交!', U('/Home/Goods/goodsInfo',array('id'=>$goods_id))); 
    }
    
    /**
     * 用户收藏某一件商品
     * @param type $goods_id
     */
    public function collect_goods($goods_id)
    {
        $goods_id = I('goods_id');
        $goodsLogic = new \Home\Logic\GoodsLogic();        
        $result = $goodsLogic->collect_goods(cookie('user_id'),$goods_id);
        exit(json_encode($result));
    }
    


    /**
     * 积分商城
     */
    public function integralMall()
    {
        $cat_id = I('get.id');
        $minValue = I('get.minValue');
        $maxValue = I('get.maxValue');
        $brandType = I('get.brandType');
        $point_rate = tpCache('shopping.point_rate');
        $is_new = I('get.is_new',0);
        $exchange = I('get.exchange',0);
        $goods_where = array(
            'is_on_sale' => 1,
        );
        //积分兑换筛选
        $exchange_integral_where_array = array(array('gt',0));
        // 分类id
        if (!empty($cat_id)) {
            $goods_where['cat_id'] = array('in', getCatGrandson($cat_id));
        }
        //积分截止范围
        if (!empty($maxValue)) {
            array_push($exchange_integral_where_array, array('lt', $maxValue));
        }
        //积分起始范围
        if (!empty($minValue)) {
            array_push($exchange_integral_where_array, array('gt', $minValue));
        }
        //积分+金额
        if ($brandType == 1) {
            array_push($exchange_integral_where_array, array('exp', ' < shop_price* ' . $point_rate));
        }
        //全部积分
        if ($brandType == 2) {
            array_push($exchange_integral_where_array, array('exp', ' = shop_price* ' . $point_rate));
        }
        //新品
        if($is_new == 1){
            $goods_where['is_new'] = $is_new;
        }
        //我能兑换
        $user_id = cookie('user_id');
        if ($exchange == 1 && !empty($user_id)) {
            $user_pay_points = intval(M('users')->where(array('user_id' => $user_id))->getField('pay_points'));
            if ($user_pay_points !== false) {
                array_push($exchange_integral_where_array, array('lt', $user_pay_points));
            }
        }

        $goods_where['exchange_integral'] =  $exchange_integral_where_array;
        $goods_list_count = M('goods')->where($goods_where)->count();
        $page = new Page($goods_list_count, 15);
        $goods_list = M('goods')->where($goods_where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $goods_category = M('goods_category')->where(array('level' => 1))->select();

        $this->assign('goods_list', $goods_list);
        $this->assign('page', $page->show());
        $this->assign('goods_list_count',$goods_list_count);
        $this->assign('goods_category', $goods_category);//商品1级分类
        $this->assign('point_rate', $point_rate);//兑换率
        $this->assign('nowPage',$page->nowPage);// 当前页
        $this->assign('totalPages',$page->totalPages);//总页数
        $this->display();
    }
    
}