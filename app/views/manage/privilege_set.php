<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
 <ul> <li class="on"><a href="<?php echo base_url('manage/priv/')?>">管理员权限管理</a></li> 
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > <form method="post"> <?php echo form_open('manage/spider/jigouEdit/'.$contentid); ?> 
                        	<div class="manage_yuyue_form"><input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash() ?>" />
                                <ul><li>美人计</li><li><input <?php echo isset($this->privilege->privilege['diary'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="diary" /></li></ul>
                                 <ul><li>美丽保</li><li><input <?php echo isset($this->privilege->privilege['meilibao'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="meilibao" /></li></ul>
                            	 <ul><li>客户记录</li><li><input <?php echo isset($this->privilege->privilege['home'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="home" /></li></ul>
                                 <ul><li>话题管理</li><li><input <?php echo isset($this->privilege->privilege['topic'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="topic" /></li></ul>
                                 <ul><li>咨询管理</li><li><input <?php echo isset($this->privilege->privilege['questions'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="questions" /></li></ul>
                                 <ul><li>推广管理</li><li><input <?php echo isset($this->privilege->privilege['tuiguang'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="tuiguang" /></li></ul>
                                 <ul><li>推荐管理</li><li><input <?php echo isset($this->privilege->privilege['tuijian'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="tuijian" /></li></ul>
                                 <ul><li>专家</li><li><input <?php echo isset($this->privilege->privilege['yishi'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="yishi" /></li></ul>
                                 <ul><li>魔镜</li><li><input <?php echo isset($this->privilege->privilege['magic'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="magic" /></li></ul>
                                 <ul><li>机构</li><li><input <?php echo isset($this->privilege->privilege['yiyuan'])?'checked="checked"':''  ?> type="checkbox" name="privs[]" value="yiyuan" /></li></ul>
                                 <ul><li>用户</li><li><input <?php echo isset($this->privilege->privilege['users'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="users" /></li><li><input type="text" value="<?php echo $users['fromv'] ?>" name="fromv" />-<input type="text" value="<?php echo $users['tv'] ?>" name="tv" /></ul>
                                 <ul><li>页面</li><li><input <?php echo isset($this->privilege->privilege['users'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="pages" /></li></ul>
                                 <ul><li>引荐人计划</li><li><input <?php echo isset($this->privilege->privilege['yinjian'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="yinjian" /></li></ul>
                                 <ul><li>统计</li><li><input <?php echo isset($this->privilege->privilege['tongji'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="tongji" /></li></ul>
                                 <ul><li>400通话记录</li><li><input <?php echo isset($this->privilege->privilege['phone400'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="phone400" /></li></ul>
                                 <ul><li>优惠信息</li><li><input <?php echo isset($this->privilege->privilege['coupon'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="coupon" /></li></ul> 
                                 <ul><li>文章管理</li><li><input <?php echo isset($this->privilege->privilege['coupon'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="article" /></li></ul>
                                 <ul><li>苹果app端广告管理</li><li><input <?php echo isset($this->privilege->privilege['apple'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="apple" /></li></ul>
                                 <ul><li>Banner管理</li><li><input <?php echo isset($this->privilege->privilege['banner'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="banner" /></li></ul>
                                 <ul><li>专题管理</li><li><input <?php echo isset($this->privilege->privilege['thematic'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="thematic" /></li></ul>
                                 <ul><li>数据管理</li><li><input <?php echo isset($this->privilege->privilege['dataManagement'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="dataManagement" /></li></ul>
                                 <ul><li>消费记录</li><li><input <?php echo isset($this->privilege->privilege['fanli'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="fanli" /></li></ul>
                                 <ul><li>提现记录</li><li><input <?php echo isset($this->privilege->privilege['tixian'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="tixian" /></li></ul>
                                 <ul><li>APP管理</li><li><input <?php echo isset($this->privilege->privilege['app'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="app" /></li></ul>
                                 <ul><li>数据抓取</li><li><input <?php echo isset($this->privilege->privilege['spider'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="spider" /></li></ul>
                                 <ul><li>应用推荐</li><li><input <?php echo isset($this->privilege->privilege['recomAPP'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="recomAPP" /></li></ul>
                                 <ul><li>项目类别</li><li><input <?php echo isset($this->privilege->privilege['category'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="category" /></li></ul>    <ul><li>苹果推送</li><li><input <?php echo isset($this->privilege->privilege['appPush'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="appPush" /></li></ul>
                                 <ul><li>站内信</li><li><input <?php echo isset($this->privilege->privilege['message'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="message" /></li></ul>
                                 <ul><li>权限设置</li><li><input <?php echo isset($this->privilege->privilege['priv'])?'checked="checked"':'' ?> type="checkbox" name="privs[]" value="priv" /></li></ul>
                                 <ul><li><button type="submit" name="submit" value="保存">保存</button></li></form>
                                <div class="clear" style="clear:both;"></div>   
</div>

                            </div>
                             
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div>
</div>
