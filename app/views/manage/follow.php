<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/AutoSuggest_2.1.3_comp.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/autosuggest_inquisitor.css" ><div class="page_content933"><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script>
            	<div class="institutions_info new_institutions_info">
                	<?php  $this->load->view('manage/leftbar'); ?>
                    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                                <li><a href="<?php echo site_url('manage/users/detail/'.$uid) ?>">用户基本资料</a></li>
                                <li><a href="<?php echo site_url('manage/users/editpass/'.$uid) ?>">修改密码</a></li>
                                <li class="selected"><a href="<?php echo site_url('manage/users/follow/'.$uid) ?>">关注</a></li>
                                <li ><a href="<?php echo site_url('manage/users/track/'.$uid) ?>">跟踪纪录</a></li>
                                <li ><a href="<?php echo site_url('manage/users/track/'.$uid) ?>">我的美人计</a></li>
                                <li ><a href="<?php echo site_url('manage/users/track/'.$uid) ?>">我的帖子</a></li>
                                <li ><a href="<?php echo site_url('manage/users/track/'.$uid) ?>">我的评论</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"><?php echo form_open("manage/users/follow"); ?>
                        	<ul> 
                            	<input type="hidden" name="uid" value="<?php echo $uid ?>" />
                            	<li>
                                	<div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">粉丝用户：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="newpass" type="text" name="fans[]" value="" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">关注方式：</div>
                                    <div class="k2"><span></span><p><input type="radio" id="f" value="1" name="follow" />被我关注 <input name="follow" id="f1" type="radio" value="2" />关注我</p></div>
                                </li>
                                <li>
                                	<div class="k1"> </div>
                                    <div class="k2"> <input style="font-size:14px; width:90px; padding:3px 5px;" type="submit" value="保存" />  </div>
                                </li>
                               
                                <div class="clear" style="clear:both;"></div>
                            </ul> <?php echo form_close(); ?>
                        </div>
                    </div> 
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 