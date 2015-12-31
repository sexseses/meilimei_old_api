<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" /><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/new.css"><script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script>
<script src="http://static.meilimei.com.cn/public/js/jquery.raty.js"></script><link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		</div> 
        <div class="page_contentnew">
        <div class="page_left">
        <dl class="con_detail con_detail_doc"> 
       		 <dt class="pos_rel visible"><img width="120" height="120" src="<?php echo $thumbUrl ?>"> 
             </dt> 
             <dd class="username"> 
            	<span> <?php echo $yisheng['username'] ?></span>  <em class="rate rvalue_<?php echo intval($yisheng['grade']/10) ?>"></em>
            </dd> 
            <dd style="height:60px;font-size:14px;"> 
            	<?php echo $yisheng['position'] ?>  
            </dd> 
            <dd> 
            	 <em class="zixun">咨询<span><?php echo $yisheng['tconsult'] ?>  </span></em>
            	 <em class="comment">评论<span><?php echo $yisheng['replys'] ?>  </span></em> 
                 <em class="ask"><a href="#askinfo" style="color:#333">向该医师提问</a></em>	
            </dd> 
           
        </dl>  <div class="tabs">
    <ul class="ctabls">
        <li class="ui-tabs-selected">个人资料</li>
        <li>在线预约</li>
        <li>客户点评</li>
    </ul>
    <div class="listinfo"><ul><li><strong>机构</strong>：<?php echo $yisheng['company'] ?></li><li><strong>简介</strong>：<?php echo $yisheng['introduce'] ?></li><li><strong>擅长</strong>：<?php echo $yisheng['skilled'] ?></li></ul><div id="askinfo" class="askinfo"><h3>向该医生提问      <span>可以输入50个字</span></h3><?php echo form_open_multipart("",array('id' => 'askform')) ?>
    <textarea style="padding:3px 5px;border:solid 1px #d6d6d6;height:60px; width:598px;" id="qtitle" name="qtitle"></textarea> 
   <div class="bung"><h3>补充问题（选填） </h3><ul style="display:none"><li><textarea style="padding:3px 5px;border:solid 1px #d6d6d6;height:60px; width:578px;" name="qdes"></textarea></li><li>
   <input type="file" name="attachPic"/></li></ul></div><input type="submit" value="" class="subqus" /><div style="clear:both"></div></form></div><div class="newsanass"><h3 style="padding:10px 0px;color:#db1765;border-bottom:dashed 1px #CCCCCC">最新解答</h3><ul>
    <?php
	foreach($questions as $r){
		echo ' <li><h4>'.$r['title'].'</h4><h5>答：'.mb_substr($r['content'],50).' <a href="'.site_url('question/'.$r['qid']).'">查看详细>></a> </h5><h6>提问者：'.$r['showname'].'<span>'.$r['cdate'].'</span></h6></li>';
	}
	?>   
   </ul></div></div>
    <div class="ui-tabs-hide yuyueinfo"><?php echo form_open_multipart("",array('id' => 'yuyueform')) ?> <dl><dt>在线预约</dt><div style="width:270px;float:left"><dd><label>姓名</label><input type="text" id="unamerequired" name="uname" /></dd>
    <dd ><label>年龄</label><input type="text" name="age" /></dd></div><div style="margin-left:20px; width:250px;float:left">
    <dd ><label>手机</label><input type="text" name="phone" id="phone" /></dd>
    <dd ><label>时间</label><input type="text" name="date" id="datepicker" /></dd></div>
    <dd style="width:532px"><label style="height:130px">备注</label><textarea name="remark" style="height:130px;width:450px; display:inline-block;border:none;"></textarea></dd>
    </dl><input type="submit" name="submit" value="提交评论" style="border:none;color:#fff;font-weight:bold;margin:10px;font-family:'微软雅黑'; float:right; background:#0577ca; padding:5px 10px;" /></form><div style="clear:both;"></div></div>
    <div class="ui-tabs-hide dianping"> <?php if($remarkstate): ?><div id="star"></div><p>请点击星星打分，每颗星代表二十分</p>
   
    <?php echo form_open_multipart("",array('id' => 'mystarform')) ?><input type="hidden" name="mystar" id="mystar" value="3" /><textarea name="commentes" style="width:600px;height:80px;">感谢<?php if (isset($yisheng['username'])) echo $yisheng['username']?>医生的详细解答</textarea>
    <input type="submit" name="submit" value="提交评论" style="border:none;color:#fff;font-weight:bold;margin:10px;font-family:'微软雅黑'; float:right; background:#0577ca; padding:5px 10px;" />
    </form><?php endif ?>
    <div style="clear:both"></div>
    <dl><dt><div style="display:inline-block;float:left;">综合评分：</div><span class="grades_<?php echo $yisheng['grade']/10 ?>"></span><div style="display:inline-block;float:right;font-size:14px; font-weight:normal"><font color="#f2497f"><?php echo $commentrows ?></font>条评论</div></dt>
    <?php
	foreach($reviews as $r){
	   echo '<dd><div><span class="ccscore cc_score_'.intval($r['score']/10).'">评分：</span><span class="ccdate">时间：'.$r['reviewdate'].'</span></div><div class="ccinfos">'.$r['review'].'</div><div class="ccuinfo">评论者：'.$r['showname'].'</div></dd></dl>';	
	}
	 
	?>   
    </div>
</div>
         </div>
        <div class="page_right"><div><img src="http://static.meilimei.com.cn/public/images/banners.png" /></div><div id="articlelist" ></div> 
        
         
        </div>
        <div  style="clear:both"></div>
        </div>
        <script>$(function(){ $.ajax({ type: "GET",url: "<?php echo base_url() ?>articles",async: true,data: "param=<?php echo $this->uri->segment(1) ?>" , success: function(data)
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
 path: '../public/images',score: 3,
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
                    expression: "if (VAL.match(/^1([0-9]{10})?$/)) return true; else return false;",
                    message: "手机号码不正确"
                }); 
				 
				jQuery("#emailcheck").validate({
                     expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "邮箱格式不正确,例如:user@meilizhensuo.com"
                });
                
            });
            /* ]]> */ 
			 
 </script>