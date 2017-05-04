<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo ($tpshop_config['shop_info_store_title']); ?></title>
    <link rel="stylesheet" href="/Template/pc/default/Static/new/css/style.css">
    <link rel="stylesheet" href="/Template/pc/default/Static/new/css/enter.css">
    <link rel="stylesheet" href="/Template/pc/default/Static/new/css/details.css">
    <link rel="stylesheet" href="/Template/pc/default/Static/new/css/base.css">
    <script type="text/javascript" src="/Template/pc/default/Static/new/js/daohang.js"></script>
    <script type="text/javascript" src="/Template/pc/default/Static/new/js/xiala.js"></script>
    <script type="text/javascript" src="/Template/pc/default/Static/new/js/jquery-1.12.3.min.js"></script>
    <script src="/Template/pc/default/Static/new/build/js/intlTelInput.js"></script>


</head>
<body  >
<header>
	<div class="top">
		<div class="top-bar">
			<div class="logo"><img src="/Template/pc/default/Static/new/img/logo.png" alt="" style="width: 100%"></div>
			<div class="title"><img src="/Template/pc/default/Static/new/img/title.png" alt="" style="width: 100%">	</div>
		</div>	
	</div>
	<div class="top-nav">
	<div class="top-cen">
	    <div class="en">
	    <?php if($_SESSION['user']['user_id']): ?><p class="entry"><a href="<?php echo U('User/index');?>"><?php echo ($_SESSION['user']['username']); ?></a></p>
	    <p class="enroll"><a href="<?php echo U('User/logout');?>">退出登录</a></p>
	    <?php else: ?>
		<p class="entry"><a href="<?php echo U('User/login');?>">请登录</a></p>
		<p class="enroll"><a href="<?php echo U('User/reg');?>">免费注册</a></p><?php endif; ?>
		</div>
		<ul>
			<li>
				<span>我的订单</span>
				<img src="/Template/pc/default/Static/new/img/arrow.png" alt="">	
				<div class="wait">
				<p><a href="">待付款</a></p>	
				<p><a href="">已付款</a></p>	
				</div>
			
			</li>
			<li><span>我的购物</span><img src="/Template/pc/default/Static/new/img/arrow.png" alt="">
			<div class="wait">
				<p><a href="">历史交易记录</a></p>	
				<p><a href="">历史浏览记录</a></p>	
				</div>
			</li>
			<li>帮助中心</a></li>
			<li><img src="/Template/pc/default/Static/new/img/phone1.png" alt="" class="car"><span>购物车</span></li>
			<li><img src="/Template/pc/default/Static/new/img/phone.png" alt="" class="telephone"><span>手机多多购</span>
				<div class="wait wait1">
				<img src="/Template/pc/default/Static/new/img/erwei.jpg" alt="" class="erwei">
				</div>
			</li>
		</ul>
		</div>
	</div>

	<div class="search">
		<div class="search-a">
		<div class="logo-duoduo">
			<img src="/Template/pc/default/Static/new/img/logo1.png" alt="" >
			<p>多多购</p>
		</div>
	
		<input type="text" class="search-bar" placeholder="请输入商品名称搜索">
		<div class="search-but">
		<a href="">
			<div class="ex-search"></div>
			<span>搜索</span>
			<div class="ex-search-1"></div>
			</a>
			</div>
		</div>
	</header>

    <ul class="nav" id="nav">
        <li class="cur nav-a"><a href="<?php echo U('Index/index');?>">首页</a></li>
        <li class="nav-a"><a href="<?php echo U('Goods/goodslist');?>">多多购物</a></li>
        <li class="nav-a"><a href="<?php echo U('BBS/bbs');?>">我的社区</a></li>
        <li class="nav-a"><a href="<?php echo U('GroupBuy/index');?>">活动优惠</a></li>
        <li class="nav-a"><a href="<?php echo U('Vip/vip');?>">会员中心</a></li>
    </ul>  
    <div class="details">
        <div class="details_cen">
            <div class="details_cen_top">
                <div class="details_cen_left"><img src="<?php echo ($goods["original_img"]); ?>" alt=""></div>
                <div class="details_cen_right">
                    <p><?php echo ($goods["goods_name"]); ?></p>
                    <div class="cen_right_price">
                        <p>市场价<i></i><i></i><del>￥<?php echo ($goods["market_price"]); ?></del></p>
                        <?php if($group_sale): ?><p>团购价<span>￥<b id='shop_price'><?php echo ($group_sale["price"]); ?></b></span></p>
                        <?php elseif($flash_sale): ?>
                        <p>限时折扣价<span>￥<b id='shop_price'><?php echo ($flash_sale["price"]); ?></b></span></p>
                        <?php else: ?>
                        <p>本店价<span>￥<b id='shop_price'><?php echo ($goods["shop_price"]); ?></b></span></p><?php endif; ?>
                        <p>国内运费<span><b>￥<?php echo ($goods["goods_post_price"]); ?></b></span></p>
                    </div>
                    <div class="delivery">
                        <div class="iteminfo_buying">
                            <div class="sys_item_spec">
                            <?php if(is_array($goods_spec)): foreach($goods_spec as $key=>$gs): if($gs['son'][0][src]): ?><dl class="clearfix iteminfo_parameter sys_item_specpara" data-sid="1" id="dl">
                                    <dt><?php echo ($gs["name"]); ?></dt>
                                    <dd>
                                        <ul class="sys_spec_img">
                                        <?php if(is_array($gs["son"])): foreach($gs["son"] as $key=>$gss): ?><li data-aid="3" id="<?php echo ($gss["id"]); ?>"><a  href="javascript:find(<?php echo ($gss["id"]); ?>,<?php echo ($gs["id"]); ?>);" title="<?php echo ($gss["item"]); ?>"><img src="<?php echo ($gss["src"]); ?>" alt="<?php echo ($gss["item"]); ?>" /></a><i></i></li><?php endforeach; endif; ?>

                                        </ul>
                                    </dd>
                                </dl>
                            <?php else: ?>
                                <dl class="clearfix iteminfo_parameter sys_item_specpara" data-sid="2" id="dl"> 
                                    <dt><?php echo ($gs["name"]); ?></dt>
                                    <dd>
                                        <ul class="sys_spec_text">
                                        <?php if(is_array($gs["son"])): foreach($gs["son"] as $key=>$gss): ?><li data-aid="3" id="<?php echo ($gss["id"]); ?>"><a  href="javascript:find(<?php echo ($gss["id"]); ?>,<?php echo ($gs["id"]); ?>);" title="<?php echo ($gss["item"]); ?>"><?php echo ($gss["item"]); ?></a><i></i></li><?php endforeach; endif; ?>
                                          
                                        </ul>
                                    </dd>
                                </dl><?php endif; endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="count">
                        <div>数量</div>
                        <?php if($flash_sale['id'] or $group_sale['id']): ?><div class="gw_num">
                                <span id='jian' ></span>
                                <input type="text" value="1" class="num"/>
                                <span  ></span>
                            </div>
                        <?php else: ?>
                        <div class="gw_num">
                                <span id='jian' class="jian">-</span>
                                <input type="text" value="1" class="num"/>
                                <span id='add' class="add">+</span>
                            </div><?php endif; ?>
                    </div>
                    <div class="buy">
                        <button onclick="addcar()">立即购买</button>
                        <button onclick="addcar(<?php echo ($goods["goods_id"]); ?>)">加入购物车</button>
                    </div>
                </div>
            </div>
            <div class="details_cen_down">
                <div class="cen_down_a">
                    <img src="/Template/pc/default/Static/new/img/arrow337.png" alt="">
                    商品描述
                </div>
                <div class="cen_down_b">
                    <ul>
                        <?php if(is_array($goodsattr)): foreach($goodsattr as $key=>$ga): ?><li><?php echo ($ga["name"]); ?>：<?php echo ($ga["value"]); ?></li><?php endforeach; endif; ?>
                       
                    </ul>
                </div>
                <div class="cen_down_c">
                    <div class="cen_down_c_img">
                        <?php echo (html_entity_decode($goods["goods_content"])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="last">
        <div class="foot">
            <ul>
                <li>

                    <div><img src="/Template/pc/default/Static/new/img/zp.jpg" alt=""></div>
                    <div>正品保障</div>
                    <div>全场正品，行货保障</div>
                </li>
                <li>
                    <div><img src="/Template/pc/default/Static/new/img/zp1.jpg" alt=""></div>
                    <div>新手指南</div>
                    <div>快速登录，无需注册</div>
                </li>
                <li>
                    <div><img src="/Template/pc/default/Static/new/img/zp2.jpg" alt=""></div>
                    <div>货到付款</div>
                    <div>货到付款，安心便捷</div>
                </li>
                <li>
                    <div><img src="/Template/pc/default/Static/new/img/zp3.jpg" alt=""></div>
                    <div>维修保障</div>
                    <div>服务保证，全国联保</div>
                </li>
                <li>
                    <div><img src="/Template/pc/default/Static/new/img/zp4.jpg" alt=""></div>
                    <div>无忧退货</div>
                    <div>无忧退货，7日尊享</div>
                </li>
                <li>
                    <div><img src="/Template/pc/default/Static/new/img/zp5.jpg" alt=""></div>
                    <div>会员权益</div>
                    <div>会员升级，尊贵特权</div>
                </li>
            </ul>
        </div>
    </div>
   <div class="footer">
    <div class="layout quality layer">
        <ul class="footer_quality">
            <li><i></i>品质保证</li>
            <li  class="f2"><i></i>7天退换 15天换货</li>
            <li  class="f3"><i></i>100元起免运费</li>
            <li  class="f4"><i></i>448家维修网点 全国联保</li>
        </ul>
    </div>
    <div class="helpful layout">
    <?php
 $md5_key = md5("select * from `__PREFIX__article_cat` where cat_id in(1,2,3,4,7)"); $result_name = $sql_result_v = S("sql_".$md5_key); if(empty($sql_result_v)) { $Model = new \Think\Model(); $result_name = $sql_result_v = $Model->query("select * from `__PREFIX__article_cat` where cat_id in(1,2,3,4,7)"); S("sql_".$md5_key,$sql_result_v,31104000); } foreach($sql_result_v as $k=>$v): ?><dl <?php if($k != 0): ?>class="jszc"<?php endif; ?> >
                <dt><?php echo ($v[cat_name]); ?></dt>
                <dd>
                    <ol>
                    	<?php
 $md5_key = md5("select * from `__PREFIX__article` where cat_id = $v[cat_id] and is_open=1"); $result_name = $sql_result_v2 = S("sql_".$md5_key); if(empty($sql_result_v2)) { $Model = new \Think\Model(); $result_name = $sql_result_v2 = $Model->query("select * from `__PREFIX__article` where cat_id = $v[cat_id] and is_open=1"); S("sql_".$md5_key,$sql_result_v2,31104000); } foreach($sql_result_v2 as $k2=>$v2): ?><li><a href="<?php echo U('Home/Article/detail',array('article_id'=>$v2[article_id]));?>" target="_blank"><?php echo ($v2[title]); ?></a></li><?php endforeach; ?>                        
                    </ol>
                </dd>
            </dl><?php endforeach; ?>
     </div>
     <div class="keep-on-record">
        <p>
        Copyright © 2016-2025 <?php echo ($tpshop_config['shop_info_store_name']); ?>  版权所有 保留一切权利 备案号:<?php echo ($tpshop_config['shop_info_record_no']); ?>
        
        <!--您好,请您给TPshop留个友情链接-->
        &nbsp;&nbsp;<a href="http://www.tp-shop.cn/">TPshop开源商城</a>
        <!--您好,请您给TPshop留个友情链接-->
        </p>
     </div>
 </div>
 

</body>

<script>
// var odl=document.getElementsByTagName('dl');
// $('#dl').click(function(){
//     console.log(odl);
// })
// //         // daohang();
        



function find(id,pid)
{
     
    if($("#"+id).hasClass("selected"))
    {
        $.post("<?php echo U('Goods/goodsInfo');?>",{id:id,pid:pid},function(data){
            if(data.status==1)
            {
                $('#shop_price').html(data.msg);
            }
            
        });
    }
    else
    {
        $.post("<?php echo U('Goods/remove');?>",{id:id,pid:pid},function(data){
            
        });
    }
}

function addcar(goodsid)
{
    var num=$(".num").val();
    $.post("<?php echo U('Goods/add_cart');?>",{num:num,goodsid:goodsid},function(data){
        alert(data);
    });

}


// //商品规格选择

$(function(){

    $(".sys_item_spec .sys_item_specpara").each(function(){

        var i=$(this);

        var p=i.find("ul>li");

        p.click(function(){

            if(!!$(this).hasClass("selected")){

                $(this).removeClass("selected");

                i.removeAttr("data-attrval");

            }else{

                $(this).addClass("selected").siblings("li").removeClass("selected");

                i.attr("data-attrval",$(this).attr("data-aid"))

            }

          

        })

    })

    

   

})

$('#add').click(function(){
    $(this).prev().val(parseInt($(this).prev().val())+1);
    var oneprice=$('#shop_price').text()
    var num=$(this).prev().val();
    allprice=oneprice/(num-1) * num;
    $('#shop_price').html(allprice);
   

});
$('#jian').click(function(){
    if($(this).next().val()>1)
    {
        $(this).next().val(parseInt($(this).next().val())-1);
        var oneprice=$('#shop_price').text()
        var num=parseInt($(this).next().val());
        one=oneprice/(num+1);
        allprice=oneprice-one;
        //alert(allprice);
        $('#shop_price').html(allprice);

    }
    
})

function fand(id){
    console.log(id);
}



</script>

</html>