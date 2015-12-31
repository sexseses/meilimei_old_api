<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/new.css"><div style="padding:0px 0px 10px">
            <div class="doc_select_t">整形医师 <em style="font-weight:normal;padding-left:10px;font-style:normal;color:#999;">  <?php echo $result['total_rows'] ?>个</em></div>
           <div class="doc_select_box">
        <dl class="clearfix">
        	<dt>城市筛选 </dt>
        	<dd class="city_list"  style="height:80px;"><a href="<?php echo $citylink ?>">不限</a>
            <?php
			function Gcity($citylink,$arr){
				$i = 1;
				foreach($arr as $r){
					echo '<a href="'.$citylink.urlencode($r).'">'.$r.'</a>'.($i%17==0?'<br>':'');
					$i++;
				}
			} 
			$arr = array('北京','深圳','上海','南京','广州','哈尔滨','呼和浩特','吉林','沈阳','石家庄','太原','天津','大连','包头','长春','鞍山','丹东',
			'福州','杭州','合肥','济南','南昌','宁波','青岛','厦门','苏州','徐州','温州','台州','金华','临沂','佳木斯',
			'武汉','长沙','南宁','郑州','东莞','佛山','宜昌','重庆','成都','贵阳','昆明','兰州','乌鲁木齐','洛阳','柳州'
			,'绵阳' ,'西宁' ,'常州' , '烟台' ,'齐齐哈尔' ,'普洱' ,'大庆' ,'唐山' ,'潍坊' ,'秦皇岛' ,'淄博','珠海','无锡','阜阳','海口',
			'西安' ,'台州' ,'威海' , '延吉' ,'邯郸' ,'绍兴' ,'宜昌' ,'保定' ,'泰安' ,'通化' ,'衡水'
			);
			Gcity($citylink,$arr);
			?>  </dd><!--<dd class="morecity">更多</dd>-->
        </dl>
                    <dl class="clearfix obj">
            	<dt>项目筛选 </dt>
                <dd>
 	<div class="obj_menu"><a href="<?php echo $itemlink ?>">不限</a> <a href="<?php echo $itemlink.'1' ?>">除皱</a><a href="<?php echo $itemlink.'2' ?>">面部轮廓<a href="<?php echo $itemlink.'3' ?>">减肥塑形</a> <a href="<?php echo $itemlink.'4' ?>">皮肤美容</a><a href="<?php echo $itemlink.'5' ?>">眼部</a><a href="<?php echo $itemlink.'6' ?>">鼻部</a><a href="<?php echo $itemlink.'8' ?>">口唇</a><a href="<?php echo $itemlink.'9' ?>">私密整形</a><a href="<?php echo $itemlink.'117' ?>">牙齿</a><a href="<?php echo $itemlink.'7' ?>">胸部</a></div>
                	
                	                	 
                	                </dd>
            </dl>
               </div> 
            </div>
		</div> 
        <div class="page_contentnew">
        <div class="page_left"><div class="lis_doctor">
        <div style="background:#f1f1f1;border-bottom:solid 1px #d6d6d6; font-size:16px; padding:3px 10px;font-weight:bold; font-family:'黑体'">医师列表</div>
        <?php 
			foreach($result['data'] as $r){
			  	echo ' <dl>
            <dt>
            	<a href="'.base_url().'yishengDetail/'.$r['user_id'].'" target="_blank">
            		<img style="height:84px;width:84px;background:url('.$r['thumbUrl'].')" src="../public/images/blank.gif" class="border">
            	</a>
            </dt>
            <dd class="doc_tit">
            	<b>
            		<a href="'.base_url().'yishengDetail/'.$r['user_id'].'" target="_blank" class="f14 f700">'.$r['username'].'</a>
            	</b>
            	<em class="rate rvalue_'.intval($r['grade']/10).'"></em>
                <em class="zixun">咨询<span>'.$r['tconsult'].'</span></em>
            	<em class="comment">评论<span>'.$r['replys'].'</span></em> 	
                <em class="viewdetail">查看详情</em> 	
            </dd> 
           <dd>'.($r['position']==''?'未填写':$r['position']).'</dd><dd><strong>机构：</strong>'.$r['company'].'</dd>
           <dd><strong>擅长：</strong>'.$r['department'].'</dd>
           <dd><ul><li><a href="'.base_url().'yishengDetail/'.$r['user_id'].'"><img  src="'.site_url().'public/images/onlinec.png" /></a></li><li><a href="'.base_url().'yishengDetail/'.$r['user_id'].'"><img src="'.site_url().'public/images/yuyueon.png" /></a></li></ul></dd>
          </dl> ';
			}			
		 ?> 
        </div><div class="pagelink"><?php echo $result['pagelink'] ?></div></div> 
        <div class="page_right"><div><img src="http://static.meilimei.com.cn/public/images/banners.png" /></div>  
        <div id="articlelist" ></div>
        <div id="bannerslist"></div>
        </div>
        <div  style="clear:both"></div>
        </div>
        <script>$(function(){  $.ajax({ type: "GET",url: "<?php echo base_url() ?>articles",async: true,data: "param=<?php echo $this->uri->segment(1) ?>" , success: function(data)
	  {if(data != ''){$("#articlelist").html(data);}}});
	  $.ajax({ type: "GET",url: "<?php echo base_url() ?>banners",async: true,data: "param=<?php echo $this->uri->segment(1) ?>&pos=3" , success: function(data)
	  {if(data != ''){$("#bannerslist").html(data);}}});
		  <!-- $(".morecity").click(function(){if($(this).text()=='收缩'){$(".city_list").css("height","23px");$(this).text('更多');}else{$(".city_list").css("height","80px");$(this).text('收缩');} });  -->   
		   $(".city_list a").each(function(){
              if($(this).text()=='<?php echo $city ?>'){
				  $(this).addClass('cur');
			  }
           }); $(".obj_menu a").each(function(){
              if($(this).text()=='<?php echo trim($item) ?>'){
				  $(this).addClass('cur');
			  }
           }); 
		 })</script>
