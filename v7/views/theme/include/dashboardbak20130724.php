<div class="Personal_center_left">
                    	<h3><?php   $roleid = $this->wen_auth->get_role_id();if($roleid==1){echo '会员账户'; 
			 $tmp = $this->db->get_where('wen_notify', array('user_id' => $this->wen_auth->get_user_id()),1 )->result(); }else if($roleid==2){echo '医师账户';}else{echo '医院账户';} ?> </h3>
                        <ul> <?php if($this->wen_auth->get_role_id()==3){ ?> <li <?php echo $this->uri->segment(2) == 'myyishi'?'class="on4"':''; ?>><a href="<?php echo base_url() ?>counselor/myyishi" class="item04">医师管理</a></li>  <?php } ?>
                            <li <?php echo($this->uri->segment(2) == 'topicjoin' || $this->uri->segment(2) == 'topic') ?'class="on1"':''; ?>><a href="<?php echo base_url() ?>user/topic" class="item01">我的话题</a></li> 
                        	<li <?php echo ($this->uri->segment(2) == 'dashboard' || $this->uri->segment(2) == 'Fquestions' || $this->uri->segment(2) == 'underway' || $this->uri->segment(1) == 'question')?'class="on13"':''; ?>><a href="<?php echo base_url() ?>user/dashboard" class="item13">我的咨询<?php if($roleid==1 and $tmp[0]->new_answer >0) echo '<div class="newans"><div class="nlf"></div><em>'.$tmp[0]->new_answer.'</em><div class="nrf"></div></div>';elseif($roleid==2 and isset($newans) and $newans >0) echo '<div class="newans"><div class="nlf"></div><em>'.$newans.'</em><div class="nrf"></div></div>';?></a></li>
                            <li <?php echo ($this->uri->segment(2) == 'info'  || $this->uri->segment(2) == 'hetong' || $this->uri->segment(2) == 'zhengshu' || $this->uri->segment(2) == 'ablum' )?'class="on2"':''; ?>><a href="<?php echo base_url() ?>user/info" class="item02">修改资料</a></li>
                            <li <?php echo $this->uri->segment(2) == 'tuijian'?'class="on4"':''; ?>><a href="<?php echo base_url() ?>counselor/tuijian" class="item04">我要推荐</a></li>
                            <?php
							if($roleid==2){						
							?>
                            <li <?php echo $this->uri->segment(2) == 'notice'?'class="on4"':''; ?>><a href="<?php echo base_url() ?>counselor/notice" class="item04">通知设置</a></li> 
						   <?php } ?>
							<?php
							if($roleid!=1){						
							?>
                            <li <?php echo ($this->uri->segment(2) == 'yuyue')?'class="on3"':''; ?>><a href="<?php echo base_url() ?>counselor/yuyue" class="item03">预约管理</a></li>
                            <li <?php echo $this->uri->segment(2) == 'tuiguang'?'class="on4"':''; ?>><a href="<?php echo base_url() ?>counselor/tuiguang" class="item04">推广管理</a></li>  
                            
                            <li <?php echo $this->uri->segment(2) == 'chongzhi'?'class="on5"':''; ?>><a href="<?php echo base_url() ?>counselor/chongzhi" class="item05">充值</a></li>
                            <?php } ?>
                            <li><a href="<?php echo base_url() ?>user/logout" class="item06">安全退出</a></li>
                        </ul>
                    </div>  