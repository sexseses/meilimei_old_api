<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
            <div class="page_content926_1">
            	<div class="register_main1">
                    <div class="register_left">
                        <div class="re_box"><?php echo form_open("user/logCheck",array('id' => 'login'))?>
                        	<ul>
                            	<li><h5>已成为美丽诊所的会员，快速登录</h5></li>
                                <li><input name="username" type="text" class="username" onfocus="if(this.value=='用户名（邮箱账号或手机号）') this.value=''" onblur="if(this.value=='') this.value='用户名（邮箱账号或手机号）'"  value="用户名（邮箱账号或手机号）"></li>
                                <li><input name="password" type="password" class="password"  value=""></li>
                                <li><input value="" name="" type="submit" class="login2_button"></li>
                            </ul><?php echo form_close()?>
                        </div>
                    </div>
                    <div class="register_right1">
                    </div>
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div>
        <div class="notice_info">
        	<ul>
            	<li>
                	<img src="http://static.meilimei.com.cn/public/images/icon1.png" width="95" height="95">
                	<h2>专业机构</h2>
                    <h5>使用美丽诊所直接<br><span>检索</span><br>你身边的美容医师</h5>
                </li>
                <li>
                	<img src="http://static.meilimei.com.cn/public/images/icon2.png" width="95" height="95">
                	<h2>了解更多的美容<br>健康信息</h2>
                    <h5><span>美丽</span>诊所涵盖全面的<span>资讯</span><br>让你在指尖私密地阅览<br>介绍和价格等</h5>
                </li>
                <li>
                	<img src="http://static.meilimei.com.cn/public/images/icon3.png" width="95" height="95">
                	<h2>分不清哪家美容医院<br>或医师更好</h2>
                    <h5>得益于一套<span>公平中立的评价体系</span><br>我们让你看到美容行业<br>真实的那一面</h5>
                </li>
                <li class="on">
                	<img src="http://static.meilimei.com.cn/public/images/icon4.png" width="95" height="95">
                	<h2>想找人聊聊变美的<br>心得和效果</h2>
                    <h5>美丽诊所专注于爱美之人的圈子<br>让大家私密而无所忌惮地<br><span>讨论美丽</span></h5>
                </li>
            </ul>
        </div> 