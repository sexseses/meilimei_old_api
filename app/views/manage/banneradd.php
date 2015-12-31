<?php  if ($msg = $this->session->flashdata('flash_message')) {
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
                            	<li><a href="<?php echo base_url('manage/banner')?>">Banner管理</a></li><li class="on"><a href="<?php echo base_url('manage/banner/add')?>">添加Banner</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                            <div class="comments"><?php echo form_open_multipart('manage/banner/add'); ?> 
                            <ul style="padding:10px;"><li style="padding:10px;"><label style="width:100px; display:inline-block">标题*</label><input name="title" type="text"  style="padding:2px;" value="" size="45" /></li> <li style="padding:10px;"><label style="width:100px; display:inline-block">标识(多个,分隔)*</label><input name="tags" type="text"  style="padding:2px;" value="" size="45" /></li><li style="padding:10px;"><label style="width:100px; display:inline-block">链接*</label><input name="link" type="text"  style="padding:2px;" value="" size="45" /></li>
<li style="padding:10px;"><label style="width:100px; display:inline-block">打开方式*</label><select name="type"><option value="">默认</option><option value="_blank">_blank</option><option value="_parent">_parent</option><option value="_self">_self</option></select></li>  
<li style="padding:10px;"><label style="width:100px; display:inline-block">显示*</label><select name="type"><option value="">默认</option><option value="phone">phone</option><option value="PC">PC</option> </select></li>   <li style="padding:10px;"><label style="width:100px; display:inline-block">位置*</label><select name="pos"><option value="0">其他</option><option value="1">顶部</option><option value="2">左</option><option value="3">右</option> </select></li>            
                            <li style="margin-top:10px;"><label s style="padding:10px;display:inline-block">内容*</label><input type="file" name="picture" /></li> 
                            <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="添加" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div>
