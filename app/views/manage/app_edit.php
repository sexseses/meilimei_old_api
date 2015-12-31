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
                            <li class="on"><a href="<?php echo base_url('manage/app')?>">APP管理</a></li><li><a href="<?php echo base_url('manage/app/add')?>">添加APP</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >   
                            <div class="comments"><?php echo form_open_multipart('manage/app/edit/'.$artid); ?> 
                            <ul style="padding:10px;"><li style="padding:10px;"><label style="width:100px; display:inline-block">名称*</label><input name="name" type="text"  style="padding:2px;" value="<?php echo $results[0]['name'] ?>" size="45" /></li>   
                            <li style="padding:10px;"><label style="width:100px; display:inline-block">附加信息*</label><input name="extras" type="text"  style="padding:2px;" value="<?php echo $results[0]['extra'] ?>" size="45" /></li>   
                              <li style="padding:10px;"><label style="width:100px; display:inline-block">版本*</label><input name="versions" type="text"  style="padding:2px;" value="<?php echo $results[0]['version'] ?>" size="45" /></li>   
                           <li style="padding:10px;"><label style="width:100px; display:inline-block">影响版本*</label><input name="effectver" type="text"  style="padding:2px;" value="<?php echo $results[0]['effectver'] ?>" size="45" /></li>   
                             <li style="padding:10px;"><label style="width:100px; display:inline-block">低版本控制*</label>
                             <select name="needupdate">
                             <option value="0" <?php echo $results[0]['needupdate']==0?'selected="selected"':'' ?> >不升级可以使用</option>
                             <option value="1" <?php echo $results[0]['needupdate']==1?'selected="selected"':'' ?>>必须升级</option>
                             <option value="2" <?php echo $results[0]['needupdate']==2?'selected="selected"':'' ?>>停止使用</option>
                             </select>
                             </li>   
                            <li><label style="width:100px; display:inline-block">APP文件*</label>
                            <input type="file" name="downurl" /> <?php echo $results[0]['downurl'] ?>
                            </li>
                            <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="更新" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div>
