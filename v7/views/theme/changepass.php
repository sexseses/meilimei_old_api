<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul><?php if($type==3): ?>
                            	<li><a href="<?php echo site_url('user/info') ?>">医院机构基本资料</a></li><li><a href="<?php echo site_url('user/hetong') ?>">合同扫描件</a></li><li><a href="<?php echo site_url('user/ablum') ?>">医院相册</a></li><li class="selected"><a href="<?php echo site_url('user/changepass/3') ?>">修改密码</a></li>
                                <?php elseif($type==2):?>
                               <li><a href="<?php echo site_url('user/info') ?>">医师基本资料</a></li><li><a href="<?php echo site_url('user/zhengshu') ?>">证书扫描件</a></li><li><a href="<?php echo site_url('user/ysablum') ?>">案例图集</a></li><li class="selected"><a href="<?php echo site_url('user/changepass/2') ?>">修改密码</a></li>
                                <?php else: ?>
                               <li><a href="<?php echo site_url('user/info') ?>">用户基本资料</a></li><li class="selected"><a href="<?php echo site_url('user/changepass/1') ?>">修改密码</a></li> <?php endif;?>
                            </ul>
                        </div>
                        <div class="personal_information"><?php echo form_open("user/changepass/".$type); ?>
                        	<ul>
                            	 <li>
                                	<div class="k1"><span>*</span>原密码：</div>
                                    <div class="k2"><input type="password" name="sourcepass" value="" /></div>
                                </li> <li>
                                	<div class="k1"><span>*</span>新密码：</div>
                                    <div class="k2"><input type="password" name="newpass" value="" /></div>
                                </li> <li>
                                	<div class="k1"><span>*</span>确认密码：</div>
                                    <div class="k2"><input type="password" name="ennewpass" value="" /></div>
                                </li>
                            	  <li>
                                	<div class="k1"> </div>
                                    <div class="k2"> <input style="font-size:14px; width:90px; padding:3px 5px;" type="submit" value="修改" />  </div>
                                </li>
                                <div class="clear" style="clear:both;"></div>
                            </ul> <?php echo form_close(); ?>
                        </div>
                    </div>    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 