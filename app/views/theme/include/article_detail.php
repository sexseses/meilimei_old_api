<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/new.css"> 
		</div> 
        <div class="page_contentnew">
        <div class="article_detail" style="width:640px; margin: 0px auto;" "><h6><a href="<?php echo site_url()?>">首页</a> >  <?php echo $results[0]['title'] ?></h6>
        <div  class="desc">
        <h2><?php echo $results[0]['title'] ?></h2>
        <span><em>作者：<?php echo $results[0]['alias'] ?></em><em>时间：<?php echo date('Y-m-d',$results[0]['cdate']) ?></em><em>来源：<?php echo $results[0]['laiyuan'] ?></em></span>
        
        </div> <div style="padding:10px 0px;font-size:14px; line-height:25px;"><?php echo $results[0]['content'] ?></div>
        
        <div class="comments_area"><?php echo form_open_multipart("",array('id' => 'comments'))?><h5><strong>发表我的评论</strong> <em>您可以输入200字</em> <?php if($notlogin){ ?><a class="button log"  href="<?php site_url() ?>user/login">登入</a><a class="button reg" href="<?php site_url() ?>user/reg">注册</a><input type="hidden" name="is_log" id="is_log" value="0" /><?php }else{ echo '<a style="padding:3px 5px; float:right;font-size:12px;font-weight:normal;color:#333" href="'.site_url('user/dashboard').'">'.$users[0]['alias'].'</a>';?> <input id="is_log" type="hidden" name="is_log" value="1" /> <?php } ?></h5>
        <textarea name="contents"></textarea><br />
        <div style="display:inline-block; float:left;padding:7px 0px;"> <input style="padding:3px 5px;width:100px;float:left" placeholder="输入验证码" id="validecode"  name="validecode" value="" type="text"> 
                             <img width="80px" style="float:left" height="25px" id="wenvalidecode" src="<?php echo site_url('checkcode/G').'?'.time() ?>" /> </div><input type="submit" class="button" style="height:30px;margin-top:8px;" value="提交评论" onclick="checkcomment()" />
                             <div style="clear:both;"></div></form>
        </div>
        </div>
        <div class="page_right">
        </div>
        <div  style="clear:both"></div>
        </div>
        <script>$(function(){  $.ajax({ type: "GET",url: "<?php echo base_url() ?>articles",async: true,data: "param=<?php echo $this->uri->segment(1) ?>" , success: function(data)
	  {if(data != ''){$("#articlelist").html(data);}}});
		    $.ajax({ type: "GET",url: "<?php echo base_url() ?>banners",async: true,data: "param=<?php echo $this->uri->segment(1) ?>" , success: function(data)
	  {if(data != ''){$("#bannerslist").html(data);}}});
		 })
        function checkcomment(){
			 if($("#is_log").val()==0){alert('还未登入不能评论');$("#comments").submit(function () {return false;});}else{return true; }
		 }
         </script>