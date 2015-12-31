<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right"> 
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo  site_url('manage/users'); ?>">普通用户管理</a></li> <li ><a href="<?php  echo site_url('manage/users/today'); ?>" >今日联系</a></li><li class="on"><a href="<?php echo site_url('manage/users/sendsms'); ?>">短信通知</a></li>       </ul>
                        </div> <div class="manage_yuyue" >
                        <div class="manage_yuyue_form" >
                        <div class="clear" style="clear:both;">请不要用此短信通知发送任何有广告性质的信息，因为服务商会禁用我们发送服务。
</div><?php echo form_open('') ?><ul> <li style="display:block;height:30px;width:100%;"><label>PhoneNum: </label><textarea style="height:20px;width:500px" name="phonenum"></textarea></p>
<li class="clear" style="display:block;height:70px;width:100%;"><label>Message: </label> <textarea style="height:50px;width:500px" name="message">感谢使用美丽神器App，我们已将你的初始密码设定为：987mei，用户名为你的手机号，登录后请更改密码。</textarea></li>
 <li><?php echo $message.'<br>余额'.$getBalance ?>
<li class="clear" style="display:block;padding:2px;width:100%;"><button id="message_submit" name="commit" class="button1" type="submit"><span><span> 发送 </span></span></button></li> 
 </ul><form>
                            </div></div>
                    </div> 
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
