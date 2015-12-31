<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937"><style type="text/css">.manage_yuyue_form label{width:150px;display:inline-block;}
.manage_yuyue_form li{display:block;width:100%;line-height:30px;}
</style>
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('manage/setting'); ?>">站点配置</a></li>
                                <li><a href="<?php echo site_url('manage/setting/email'); ?>">邮件模板</a></li> 
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form">
                            <?php echo form_open('manage/setting'); ?> 
                              <h3>积分设置</h3>
                            	<ul>
                                <li><label>发布话题</label><input type="text" name="JIFEN_WEIBO" value="<?php echo $results['JIFEN_WEIBO'] ?>" /></li>
                                <li><label>回复话题</label><input type="text" name="JIFEN_RWEIBO" value="<?php echo $results['JIFEN_RWEIBO'] ?>" /></li>
                                <li><label>发布咨询</label><input type="text" name="JIFEN_ZIXUN" value="<?php echo $results['JIFEN_ZIXUN'] ?>" /></li>
                                <li><label>回复咨询</label><input type="text" name="JIFEN_RZIXUN" value="<?php echo $results['JIFEN_RZIXUN'] ?>" /></li>
                                <li><label>注册可获得积分</label><input type="text" name="JIFEN_REG" value="<?php echo $results['JIFEN_REG'] ?>" /></li> 
                                </ul> 
                                 <button name="updatejifen" value="1" type="submit">更新积分设置</button>
                                 <div class="clear" style="clear:both;"></div>
                                 <h3>成长值设置</h3>
                                 <ul>
                                <li><label>发帖</label><input type="text" name="GROW_TOPIC" value="<?php echo $results['GROW_TOPIC'] ?>" /></li>
                                <li><label>回帖</label><input type="text" name="GROW_RTOPIC" value="<?php echo $results['GROW_RTOPIC'] ?>" /></li>
                                <li><label>被回复</label><input type="text" name="GROW_RFTOPIC" value="<?php echo $results['GROW_RFTOPIC'] ?>" /></li>
                                <li><label>每日签到</label><input type="text" name="GROW_ATTEND" value="<?php echo $results['GROW_ATTEND'] ?>" /></li>
                                <li><label>选为达人贴</label><input type="text" name="GROW_SPWEIBO" value="<?php echo $results['GROW_SPWEIBO'] ?>" /></li>
                                <li><label>每日限额</label><input type="text" name="GROW_LIMIT" value="<?php echo $results['GROW_LIMIT'] ?>" /></li>
                                 </ul>
                                  <button name="updategrow" value="1" type="submit">更新成长值设置</button>
                                <div class="clear" style="clear:both;"></div> 
                                <h3>会员等级</h3>
                               <dl><dd style="width:170px;float:left;">头衔</dd><dd style="width:320px;float:left;">成长值</dd><dd style="width:100px;float:left;">折扣</dd></dl>
                                <ul>
                                <?php
								foreach($grade as $r){
								  echo '<li><input type="text" name="group['.$r['groupid'].']" value="'.$r['grouptitle'].'" />
								  <input style="width:50" type="text" name="groupv1['.$r['groupid'].']" value="'.$r['creditshigher'].'" />-
								  <input style="width:50" type="text" name="groupv2['.$r['groupid'].']" value="'.$r['creditslower'].'" />
								  <input style="width:30" type="text" name="groupv3['.$r['groupid'].']" value="'.$r['discount'].'" />
								  <a href="'.site_url('manage/setting/grade_edit/'.$r['groupid']).'">编辑</a>
								  </li>';							 
								}
								?>
                                </ul>
                                <div class="clear" style="clear:both;"></div>
                                <button name="updategrade" value="1" type="submit">更新会员等级</button>
                            <?php echo form_close(); ?> 
                            </div>
                             
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div> 