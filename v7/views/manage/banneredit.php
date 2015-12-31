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
                            	<li><a href="<?php echo base_url('manage/banner')?>">Banner管理</a></li><li ><a href="<?php echo base_url('manage/banner/add')?>">添加Banner</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                            <div class="comments"><?php echo form_open_multipart('manage/banner/edit/'.$bannerid); ?> 
                            <ul style="padding:10px;"><li style="padding:10px;"><label style="width:100px; display:inline-block">标题*</label><input name="title" type="text"  style="padding:2px;" value="<?php echo $results[0]['title'] ?>" size="45" /></li> <li style="padding:10px;"><label style="width:100px; display:inline-block">标识(多个,分隔)*</label><input name="tags" type="text"  style="padding:2px;" value="<?php echo $results[0]['tags'] ?>" size="45" /></li><li style="padding:10px;"><label style="width:100px; display:inline-block">链接*</label><input name="link" type="text"  style="padding:2px;" value="<?php echo $results[0]['link'] ?>" size="45" /></li>
<li style="padding:10px;"><label style="width:100px; display:inline-block">打开方式*</label><select name="type"><option <?php echo $results[0]['type']==''?'selected="selected"':'' ?>  value="">默认</option><option <?php echo $results[0]['type']=='_blank'?'selected="selected"':'' ?> value="_blank">_blank</option><option <?php echo $results[0]['type']=='_parent'?'selected="selected"':'' ?> value="_parent">_parent</option><option <?php echo $results[0]['type']=='_self'?'selected="selected"':'' ?> value="_self">_self</option></select></li><input name="sourcefile" type="hidden" value="<?php echo $results[0]['picture'] ?>" />  
<li style="padding:10px;"><label style="width:100px; display:inline-block">显示*</label><select name="machine"><option value="" <?php echo $results[0]['machine']==''?'selected="selected"':'' ?>>默认</option><option value="phone" <?php echo $results[0]['machine']=='phone'?'selected="selected"':'' ?>>phone</option><option value="PC" <?php echo $results[0]['machine']=='PC'?'selected="selected"':'' ?>>PC</option> </select></li><li style="padding:10px;"><label style="width:100px; display:inline-block">位置*</label><select name="pos"><option <?php echo $results[0]['pos']==0?'selected="selected"':'' ?> value="0">其他</option><option value="1" <?php echo $results[0]['pos']==1?'selected="selected"':'' ?> >顶部</option><option value="2" <?php echo $results[0]['pos']==2?'selected="selected"':'' ?> >左</option><option value="3" <?php echo $results[0]['pos']==3?'selected="selected"':'' ?> >右</option> </select></li>
 <li style="margin-top:10px;"><label style="padding:10px;width:90px;display:inline-block">权重*</label> <input type="text" name="weigh" value="<?php echo $results[0]['weigh'] ?>" /></li>                
                            <li style="margin-top:10px;"><label style="padding:10px;display:inline-block">内容*</label><input type="file" name="picture" /><img src="<?php echo site_url().$results[0]['picture'] ?>" style="max-width:300px;" /></li> 
                            <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="更新" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div>
