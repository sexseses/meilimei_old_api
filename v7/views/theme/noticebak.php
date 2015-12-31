<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">通知设置</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"> <?php echo form_open_multipart("",array('id' => 'reg'))?>
                        	<ul>
                            	<li>
                                	<div class="k1" style="font-weight:normal"> 接收用户咨询的邮件提醒：</div>
                                    <div class="k2"><span> </span> <p><input type="radio" <?php echo $notification[0]->new_ask?'checked="checked"':'' ?>  name="zixun" value="1" />打开  <input <?php echo $notification[0]->new_ask?'':'checked="checked"' ?> type="radio" name="zixun" value="0" />关闭</p></div>
                                </li>
                            	 
                                <li>
                                	<div class="k1" style="font-weight:normal"> 接收用户在同个咨询中追问的邮件提醒：</div>
                                    <div class="k2"><span></span><p><input type="radio" <?php echo $notification[0]->new_reply?'checked="checked"':'' ?> name="szixun" value="1" />打开  <input <?php echo $notification[0]->new_reply?'':'checked="checked"' ?> type="radio" name="szixun" value="0" />关闭</p> </span></div>
                                </li>
                                 <li>
                                	<div class="k1" style="font-weight:normal">  </div>
                                    <div class="k2"><input type="submit" name="save" value="保存" style="padding:2px 3px;cursor:pointer;"/></div>
                                </li>
                                <div class="clear" style="clear:both;"></div>
                            </ul>  </form>
                        </div>
                    </div> <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 