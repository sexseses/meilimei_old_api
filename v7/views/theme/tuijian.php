<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li class="selected"><a href="#">推荐信息</a></li><li><a href="<?php echo site_url('counselor/tongji') ?>">统计报表</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"> 
                        	<ul>
                            	<li>
                                	<div class="k1"><span>*</span>网址二维码：</div>
                                    <div class="k2"><span> </span> <p><img id="thumbPic" src="<?php echo $image ?>" width="120" ></p><p>图片地址：<?php echo $image ?></p></div>
                                </li>
                            	 
                                <li>
                                	<div class="k1"><span>*</span>推荐的网址：</div>
                                    <div class="k2"><span></span><p><?php echo $url ?></p><span><button id="copy-button" data-clipboard-text="<?php echo $url ?>" title="点击复制.">复制网址</button>
  </span></div>
                                </li>
                                  <li>
                                	<div class="k1"><span> </span>跳转网址：</div>
                                    <div class="k2"><?php echo form_open('counselor/tuijian'); ?> <p style="display:inline-block;float:left;"><input type="text" value="<?php echo isset($results[0])?$results[0]->url:''; ?>" id="jurl" maxlength="120" style="padding:2px 5px;width:350px" width="50" name="jurl" /><br /><input type="submit" style="padding:2px 5px; margin-top:3px;" value="设置" /><input type="hidden" name="codes" value="<?php echo $aucode ?>" /></p><span style="font-size:11px; display:inline-block; padding-bottom:15px;">(可不填使用默认跳转统计) </span></form></div>
                                </li>
                                <div class="clear" style="clear:both;"></div>
                            </ul>  
                        </div>
                    </div> <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/ZeroClipboard.min.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/mains.js"></script>