<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/new.css"><div style="padding:0px 0px 10px">
            <div class="doc_select_t">整形医院
<em style="font-weight:normal;padding-left:10px;font-style:normal;color:#999;">  <?php echo $result['total_rows'] ?>个</em>
      		      	</div>
           <div class="doc_select_box">
        <dl class="clearfix">
        	<dt>城市筛选 </dt>
        	<dd class="city_list" style="height:100px;"><a href="<?php echo $citylink ?>">上海</a> <?php
         
            function Gcity($citylink,$arr,$city='上海'){
				$i = 1;
				foreach($arr as $k=>$r){
					 //echo $r;
					 
					if($city == $r){ 
						$class_action = "cur";
					}else{
						$class_action = "";
					}

					echo '<a class="'.$class_action.'" href="'.$citylink.urlencode($r).'">'.$r.'</a>'.($i%17==0?'<br>':'');
					$i++;
				}
			} 
			$arr = array('北京','上海','杭州','广州','深圳','哈尔滨','呼和浩特','吉林','沈阳','石家庄','太原','天津','大连','长春','鞍山',
			'福州','合肥','济南','南昌','南京','宁波','青岛','厦门','苏州','徐州','温州','台州','金华','临沂','保定','海口','牡丹江',
			'武汉','长沙','南宁','郑州','东莞','佛山','宜昌','重庆','成都','贵阳','昆明','兰州','乌鲁木齐','洛阳','唐山' ,'柳州','丹东'
			,'绵阳' ,'西宁' ,'常州' ,'包头' ,'烟台' ,'齐齐哈尔' ,'普洱' ,'大庆' , '潍坊' ,'秦皇岛' ,'淄博','珠海','无锡','阜阳',
			'佳木斯' ,'延吉' ,'通化' ,'衡水' ,'邯郸' ,'绍兴' ,'泰安' ,'威海' ,'西安'
			);
 
			Gcity($citylink,$arr,$now_city);
			?></dd><!--<dd class="morecity">更多</dd>-->
        </dl>
                     
               </div> 
            </div>
		</div> 
        <div class="page_contentnew">
        <div class="page_left"><div class="lis_doctor lis_yiyuan">
        <div style="background:#f1f1f1;border-bottom:solid 1px #d6d6d6; font-size:16px; padding:3px 10px;font-weight:bold; font-family:'黑体'">医院列表</div>
        <?php 
			foreach($result['data'] as $r){
			  	echo ' <dl>
            <dt>
            	<a href="'.base_url().'jigouDetail/'.$r['user_id'].'" target="_blank">
            		<img width="84" src="'.$r['thumbUrl'].'" data-original="'.$r['thumbUrl'].'" class="border load-delay">
            	</a>
            </dt>
            <dd class="doc_tit">
            	<b style="display:inline-block;float:left;">
            		<a title="'.$r['username'].'" href="'.base_url().'jigouDetail/'.$r['user_id'].'" target="_blank" class="f14 f700">'.$r['username'].'</a>
            	</b>'.($r['verify']?'
				<em class="renzheng-yy"></em>':'') .($r['suggested']?'
            	<em class="suggest-yy"></em>':'').'
                <em class="viewdetail" style="display:inline-block;float:right;">查看详情</em> 	
            	<em class="comment" style="display:inline-block;float:right;">评论<span>'.$r['replys'].'</span></em> 	
               
            </dd> 
		   <dd class="favorites"><strong style="display:inline-block;float:left;">评分：</strong><em class="rate rvalue_'.intval($r['grade']/10).'"></em></dd>
           <dd><strong>地址：</strong>'.$r['address'].'</dd>
           <dd><strong>电话：</strong>'.$r['tel'].'</dd>
           <dd class="yiyuan"><ul> <li><a href="'.base_url().'jigouDetail/'.$r['user_id'].'"><img src="http://static.meilimei.com.cn/public/images/yuyueon.png" /></a></li></ul></dd>
          </dl> ';
			}			
		 ?> 
        </div><div class="pagelink"><?php echo $result['pagelink'] ?></div></div>
        <div class="page_right">
            <div>
                <img src="http://static.meilimei.com.cn/public/images/zzjgxj.png" />
            </div>
            <div>
                <img src="http://static.meilimei.com.cn/public/images/zdbh.png" />
            </div>
            <div>
                <img src="http://static.meilimei.com.cn/public/images/pzsccld.png" />
            </div>
        </div><div  style="clear:both"></div>
        <!--           <div id="articlelist" >
            
          </div> 
         <div id="bannerslist"></div> -->
        </div>
        <script>$(function(){  $.ajax({ type: "GET",url: "<?php echo base_url() ?>articles",async: true,data: "param=<?php echo $this->uri->segment(1) ?>" , success: function(data)
	  {if(data != ''){$("#articlelist").html(data);}}});
	   $.ajax({ type: "GET",url: "<?php echo base_url() ?>banners",async: true,data: "param=<?php echo $this->uri->segment(1) ?>&pos=3" , success: function(data)
	  {if(data != ''){$("#bannerslist").html(data);}}});
		   <!--$(".morecity").click(function(){if($(this).text()=='收缩'){$(".city_list").css("height","23px");$(this).text('更多');}else{$(".city_list").css("height","80px");$(this).text('收缩');} });-->
		        $(".city_list a").each(function(){
              if($(this).text()=='<?php echo $city ?>'){
				  $(this).addClass('cur');
			  }
           }); $(".obj_menu a").each(function(){
              if($(this).text()=='<?php echo trim($item); ?>'){
				  $(this).addClass('cur');
			  }
           }); 
		 })
		 
}); 
</script>