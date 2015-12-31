<?php if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> <script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
<script>
		KindEditor.ready(function(K) {
		    window.editor = K.create('#content');
		});
</script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li>
									<a href="<?php echo site_url('manage/tehuibanner')?>">Banner管理</a>
								</li>
								<li class="on">
									<a href="<?php echo site_url('manage/tehuibanner/topadd')?>">添加Banner</a>
								</li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                            <div class="comments"><?php echo form_open_multipart('manage/tehuibanner/topinset'); ?> 
                            <ul style="padding:10px;">
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">标题</label>
									<input name="title" type="text"  style="padding:2px;" value="" size="45" />
								</li> 
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">内容</label>
									<input name="conn" type="text"  style="padding:2px;" value="" size="45" />
								</li>
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">权重</label>
									<input name="weigt" type="text"  style="padding:2px;" value="" size="10" />
								</li>
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">美人记</label>
									<input name="meirenji" type="text"  style="padding:2px;" value="" size="10" />
								</li>
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">社区帖子</label>
									<input name="tiezi" type="text"  style="padding:2px;" value="" size="10" />
								</li>
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">闪购</label>
									<input name="shangou" type="text"  style="padding:2px;" value="" size="10" />
								</li>
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">特惠</label>
									<input name="trme" type="text"  style="padding:2px;" value="" size="10" />
								</li>
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">链接</label>
									<input name="link" type="url"  style="padding:2px;" value="" size="45" />
								</li>	
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">位置</label>
									<input type="checkbox" name="weizhi[]" value="falsh"/>闪购
									<input type="checkbox" name="weizhi[]" value="tehui"/>特惠
								</li>		
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">显示</label>
									<input type="checkbox" name="showtype[]" value="ios"/>ios
									<input type="checkbox" name="showtype[]" value="android"/>android
								</li>															
								<li style="margin-top:10px;">
									<label s style="padding:10px;display:inline-block">banner图*</label>
									<input type="file" name="lbanner" />
								</li>
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">内容</label>
									<textarea id="content" style="padding:1px;width:700px;height:550px;" name="content"></textarea>
								</li>
								<li style="padding:10px;">
									<label style="width:100px; display:inline-block">地区*</label>
									<?php foreach($results as $row){ 
									echo '									
									<input type="checkbox" name="dizhi[]" value="@'.$row['czone'].'@" />
									'.$row['czone'].'
									';									
									}
									?>
								</li>					
								<li style="padding:10px 10px 10px 100px;">
									<input type="submit" name="submit" value="添加" style="padding:2px 10px;" />
								</li>
                            </ul>
                            <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div>

