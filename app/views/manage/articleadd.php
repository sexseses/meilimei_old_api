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
                            	<li><a href="<?php echo base_url('manage/article')?>">文章管理</a></li><li class="on"><a href="<?php echo base_url('manage/article/add')?>">添加文章</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >   
                            <div class="comments"><?php echo form_open_multipart('manage/article/add'); ?> 
                            <ul style="padding:10px;"><li style="padding:10px;"><label style="width:100px; display:inline-block">标题*</label><input name="title" type="text"  style="padding:2px;" value="" size="45" /></li><li style="padding:10px;"><label style="width:100px; display:inline-block">缩略图*</label><input name="picture" type="file"  size="45" /></li><li style="padding:10px;"><label style="width:100px; display:inline-block">标识(使用,分隔)*</label><input name="tags" type="text"  style="padding:2px;" value="" size="45" /></li><li style="padding:10px;"><label style="width:100px; display:inline-block">描述*</label><textarea style="width:550px" name="dec"></textarea> </li>
                            <li style="padding:10px;"><label style="width:100px; display:inline-block">关键字</label><textarea style="width:550px" name="keywords"></textarea> </li>
                            
                            <li style="padding:10px;"><label style="width:100px; display:inline-block">来源*</label><input name="laiyuan" type="text"  style="padding:2px;" value="转载" size="45" /></li>
                            
                            <li style="padding:10px;"><label style="width:100px; display:inline-block">分类*</label><?php foreach($consult_cat as $k){ ?><li><input name="consult_cat[]" type="checkbox"  style="padding:2px;" value="<?php echo $k['id']?>" size="45" /><?php echo $k['cat_name']; ?></li><?php  }?></li>
  
                            
                            <li style="margin-top:10px;"><label style="width:100px; display:inline-block">内容*</label> <textarea id="content" style="padding:1px;width:700px;height:550px;" name="content"></textarea></li> 
                            <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="添加" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div>
