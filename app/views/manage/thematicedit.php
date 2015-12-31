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
                            	<li><a href="<?php echo base_url('manage/thematic')?>">专题管理</a></li><li><a href="<?php echo base_url('manage/thematic/add')?>">添加专题</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                            <div class="comments"><?php echo form_open_multipart('manage/thematic/edit/'.$id); ?> 
                            <ul style="padding:10px;"><li style="padding:10px;"><label style="width:100px; display:inline-block">标题*</label><input name="title" type="text"  style="padding:2px;" value="<?php echo $results[0]['title'];?>" size="45" /></li> <li style="padding:10px;"><label style="width:100px; display:inline-block">标识(多个,分隔)*</label><input name="tags" type="text"  style="padding:2px;" value="<?php echo $results[0]['tags'];?>" size="45" /></li> 
                            <li style="padding:10px;"><label style="width:100px; display:inline-block">Header显示</label><select name="showtype"><option <?php echo  $results[0]['showtype']==0?'selected="selected"':'' ?> value="0">不显示</option><option <?php echo $results[0]['showtype']==1?'selected="selected"':'' ?> value="1">显示</option></select></li>
<li style="padding:10px;"><label style="width:100px; display:inline-block">描述*</label><textarea name="descm" style="width:580px;height:80px;"><?php echo $results[0]['descm'];?></textarea></li>                     
                            <li style="margin-top:10px;"><label s style="padding:10px;display:inline-block">图片*</label><input type="file" name="picture" /> <img src="../../../<?php echo $results[0]['picture'];?>" style="max-width:100px" /></li> 
                            <li style="margin-top:10px;"><label style="width:100px; display:inline-block">内容*</label> <textarea id="content" style="padding:1px;width:700px;height:550px;" name="content"><?php echo $results[0]['contents'];?></textarea></li> 
                            <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="更新" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div>
