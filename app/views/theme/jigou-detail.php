<link rel="stylesheet" href="http://static.meilimei.com.cn/public/css/jquery-ui.css" /><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/new.css"><script src="http://static.meilimei.com.cn/public/js/jquery-ui.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script>
<script src="http://static.meilimei.com.cn/public/js/jquery.raty.js"></script> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.ad-gallery.js"></script>
		</div> 
        <div class="page_contentnew">
        <div class="page_left">
        <dl class="con_detail con_detail_doc" style="border:solid 1px #d6d6d6;;border-bottom:none;height:180px;"> 
       		 <h3 style="background:#f1f1f1;border-bottom:solid 1px #d6d6d6;padding:3px 5px; margin-bottom:20px;">医疗美容机构</h3>
             <dd class="username" style="padding-left:10px;"> 
            	<span> <?php echo $yisheng['name'].($yisheng['verify']?'
				<em class="renzheng-yy"></em>':'') .($yisheng['suggested']?'
            	<em class="suggest-yy"></em>':'')  ?></span> 
            </dd> 
            <dd style="padding-left:10px;"><strong style="display:inline-block;float:left;">评分:</strong>  <em class="rate rvalue_<?php echo intval($yisheng['grade']/10) ?>"></em></dd>
            <dd style="padding-left:10px;"><strong style="display:inline-block;float:left;padding-right:10px;">地址: </strong>   <?php echo $yisheng['address'] ?></dd>
            <dd style="padding-left:10px;"><strong style="display:inline-block;float:left;padding-right:10px;">电话: </strong>   <?php echo $yisheng['tel'] ?></dd> 
           
        </dl>  <div class="tabs">
    <ul class="ctabls" style="margin:0px;">
        <li class="ui-tabs-selected">机构简介</li>
        <li>限时特惠</li>
        <li>客户点评</li><li>机构图集</li>
    </ul>
    <div class="listinfo"><ul> <li><strong>简介</strong>：<?php echo $yisheng['descrition'] ?></li> </ul><div id="askinfo" class="askinfo yuyueinfo"><?php echo form_open_multipart("",array('id' => 'yuyueform')) ?> <dl><dt>在线预约</dt><div style="width:270px;float:left"><dd><label>姓名</label><input type="text" id="unamerequired" name="uname" /></dd>
    <dd ><label>年龄</label><input type="text" name="age" /></dd></div><div style="margin-left:20px; width:250px;float:left">
    <dd ><label>手机</label><input type="text" name="phone" id="phone" /></dd>
    <dd ><label>时间</label><input type="text" name="date" id="datepicker" /></dd></div>
    <dd style="width:532px"><label style="height:130px">备注</label><textarea name="remark" style="height:130px;width:450px; display:inline-block;border:none;"></textarea></dd>
    </dl><input type="submit" name="submit" value="提交评论" style="border:none;color:#fff;font-weight:bold;margin:10px;font-family:'微软雅黑'; float:right; background:#0577ca; padding:5px 10px;" /></form><div style="clear:both;"></div></div> </div>
    <div class="ui-tabs-hide yuyueinfo"></div>
    <div class="ui-tabs-hide dianping"> <?php if($remarkstate): ?><div id="star"></div><p>请点击星星打分，每颗星代表二十分</p>
   
    <?php echo form_open_multipart("",array('id' => 'mystarform')) ?><input type="hidden" name="mystar" id="mystar" value="3" /><textarea name="commentes" style="width:600px;height:80px;"> </textarea>
    <input type="submit" name="submit" value="提交评论" style="border:none;color:#fff;font-weight:bold;margin:10px;font-family:'微软雅黑'; float:right; background:#0577ca; padding:5px 10px;" />
    </form><?php endif ?> 
    <div style="clear:both"></div>
    <dl><dt><div style="display:inline-block;float:left;">综合评分：</div><span class="grades_<?php echo $yisheng['grade']/10 ?>"></span><div style="display:inline-block;float:right;font-size:14px; font-weight:normal"><font color="#f2497f"><?php echo $commentrows ?></font>条评论</div></dt>
    <?php
	foreach($reviews as $r){
	   echo '<dd><div><span class="ccscore cc_score_'.intval($r['score']/10).'">评分：</span><span class="ccdate">时间：'.$r['reviewdate'].'</span></div><div class="ccinfos">'.$r['review'].'</div><div class="ccuinfo">评论者：'.$r['showname'].'</div></dd></dl>';	
	}
	 
	?>   
    </div>  <div class="ui-tabs-hide" style="width:630px;border:solid 1px #d6d6d6;padding:10px;"><?php if($ablum_state): ?><div id="gallery" class="ad-gallery">
      <div class="ad-image-wrapper">
      </div>
      <div class="ad-controls">
      </div>
      <div class="ad-nav">
        <div class="ad-thumbs">
          <ul class="ad-thumb-list"><?php
		   foreach($ablum as $r){
			   echo '<li><a href="'.$r.'"><img src="'.$r.'" class="image0"></a></li>';		   } 
		  ?> 
          </ul> 
        </div>
      </div>
    </div><?php endif;?>
    <div id="descriptions">

    </div></div>
</div>
         </div>
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
        </div>
        <div  style="clear:both"></div>
        </div>
        <script>$(function(){ var galleries = $('.ad-gallery').adGallery({width:580, height: 400, });
          $.ajax({ type: "GET",url: "<?php echo base_url() ?>articles",async: true,data: "param=<?php echo $this->uri->segment(1) ?>" , success: function(data)
	  {if(data != ''){$("#articlelist").html(data);}}});
	  $.ajax({ type: "GET",url: "<?php echo base_url() ?>banner",async: true,data: "param=<?php echo $this->uri->segment(1) ?>" , success: function(data)
	  {if(data != ''){$("#articlelist").html(data);}}});
			$( "#datepicker" ).datepicker();
		    $(".tabs").find("li").click(function(e) {
            if (e.target == this) {
                var tabs = $(this).parent().children("li");
                var panels = $(this).parent().parent().children("div");
                var index = $.inArray(this, tabs);
                if (panels.eq(index)[0]) {
                    tabs.removeClass("ui-tabs-selected")
                        .eq(index).addClass("ui-tabs-selected");
                    panels.addClass("ui-tabs-hide")
                        .eq(index).removeClass("ui-tabs-hide");
                }
            } 
        });
         $(".bung h3").click(function(){
			$(".bung ul").toggle(300);
		 });
		 $('#star').raty({
                score: function() {
              return $(this).attr('data-score');
          }
		  
});    $("div#star").raty({
 path: 'http://static.meilimei.com.cn/public/images',score: 3,
showHalf: true,click: function(score, evt) {$("#mystar").val(score);}})
     
		 })  
 
 
            /* <![CDATA[ */
            jQuery(function(){
                jQuery("#unamerequired").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "姓名不能为空"
                });
				
				jQuery("#qtitle").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });jQuery("#phone").validate({
                    expression: "if (!VAL.match(/^((\(\d{3}\))|(\d{3}\-))?13\d{9}|18\d{9}|15[089]\d{8}$/)) return true; else return false;",
                    message: "手机号码不正确"
                }); 
				 
				jQuery("#emailcheck").validate({
                     expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "邮箱格式不正确,例如:user@meilizhensuo.com"
                });
                
            });
            /* ]]> */ 
			 
 </script>