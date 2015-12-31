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
                            	<li class="on"><a href="<?php echo base_url('manage/pages/')?>">页面管理</a></li><li><a href="<?php echo base_url('manage/pages/add')?>">添加页面</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >   
                            <div class="comments"><?php echo form_open('manage/pages/add'); ?> 
                            <ul style="padding:10px;"><li style="padding:10px;"><label style="width:100px; display:inline-block">标题</label><input name="title" type="text"  style="padding:2px;" value="<?php echo $results[0]->title ?>" size="45" /></li>
                            <li style="padding:10px;"><label style="width:100px; display:inline-block">URL</label><input name="alias" type="text"  style="padding:2px;" value="<?php echo $results[0]->alias ?>" size="45" /></li>
                         <li style="padding:10px;"><label style="width:100px; display:inline-block">Header显示</label><select name="showtype"><option <?php echo  $results[0]->showtype==0?'selected="selected"':'' ?> value="0">不显示</option><option <?php echo $results[0]->showtype==1?'selected="selected"':'' ?> value="1">显示</option></select></li>
                         <li style="padding:10px;"><label style="width:100px; display:inline-block">页面显示</label><select name="status"><option <?php echo $results[0]->status==0?'selected="selected"':'' ?> value="0">不显示</option><option <?php echo $results[0]->status==1?'selected="selected"':'' ?>  value="1">显示</option></select></li>
                            <li style="margin-top:10px;"><label style="width:100px; display:inline-block">内容</label> <textarea id="content" style="padding:1px;width:700px;height:550px;" name="content"><?php echo $results[0]->content ?></textarea></li> 
                            <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="添加" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div>
