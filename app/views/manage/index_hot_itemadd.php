<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/index_hot_item'); ?>">热门项目管理</a></li>
                            	<?php if(!$results){?>
                                <li class="on"><a href="<?php echo site_url('manage/index_hot_item/itemadd'); ?>">添加项目</a></li>
                            <?php }?>
                            </ul>
                        </div>
                      <div class="manage_yuyue" >
                       <div class="comments"><?php echo form_open_multipart('manage/index_hot_item/itemuplode'); ?> 
                            <ul style="padding:10px;">
                                <li style="padding:10px;">
                                    <label style="width:100px; display:inline-block">标题*</label>
                                    <input name="item_name" type="text"  style="padding:2px;" value="<?php echo $results['item_name']; ?>" size="45" />
                                </li> 
                                 <li style="padding:10px;">
                                    <label style="width:100px; display:inline-block">等级*</label>
                                    <input name="level" type="text"  style="padding:2px;" value="<?php echo $results['level']; ?>" size="45" />
                                 </li>
                                <li style="margin-top:10px;">
                                    <?php if($results){?>
                                    <input type="hidden" name="uid" value="<?php echo $results['id']; ?>" />
                                    <input type="hidden" name="hdpic" value="<?php echo $results['pic']; ?>" />
                                    <img width="150px" height="200px" src="http://pic.meilimei.com.cn/upload/<?php echo $results['pic']; ?>"/>
                                    <?php }?>
                                    <label style="padding:10px;display:inline-block">图片*</label>
                                    <input type="file" name="picfile" />
                                </li> 
                                <li style="padding:10px;">
                                    <label style="width:100px; display:inline-block">城市*</label>
                                     <label >
                                 </li>
                                 <li style="font-size: 15px;">
                                    <?php foreach($city as $item) {?>
                                    <input type="checkbox" name="city[]"  value="<?php echo $item['city'];?>" <?php if(in_array($item['city'], $item_city)) { echo "checked='checked'";}?>/>
                                    <label for="c<?php echo $item['id'];?>"><?php echo $item['city'];?></label><?php } ?>
                                    </label>
                                 </li>
                                <li style="padding:10px 10px 10px 100px;">
                                    <input type="submit" name="submit" value="添加" style="padding:2px 10px;" />
                                </li>
                              </ul>
                            </form>
                        </div>
                       </div>
                    </div>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
