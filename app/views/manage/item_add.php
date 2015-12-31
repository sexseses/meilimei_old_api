<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> <script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('.editer');
        });
</script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo base_url('manage/category/')?>">项目管理</a></li><li class="on"><a>添加项目</a></li>
                            </ul>
                        </div> <style type="text/css">dd label{display:inline-block;padding:0px 5px;} dd{display:block;line-height:30px;height:30px;margin-bottom:5px;}</style>
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form">
                        		<?php echo form_open_multipart(); ?>
                        		<input type="hidden" name="item_pid" value="<?php echo $item_id;  ?>" />
                        		<input type="hidden" name="act" value="add" />
                        		<table>
                        			<tr>
                        				<td>名称</td>
                        				<td><input type="text" value=" " name="item_name" /></td>
                        			</tr>
                        			<tr>
                        				<td>请选择父类</td>
                        				<td>
                        					<ul>
		                                		<?php foreach ($items_result as $key => $value) { ?>
		                                			<li>
		                                				<input type="radio" name="item" value ="<?php echo $value['id']; ?> " <?php if($value['id'] == $item_id){ ?>  checked = "checked" <?php } ?>  />
		                                			</li>
		                                			<li><?php echo $value['name'] ?></li>
		                                		<?php } ?>
	                                		</ul>
                        				</td>
                        			</tr>
                        			<tr>
                        				<td>小图</td>
                        				<td><input type="file" name="surl" />  90*90</td>
                        			</tr>
                        			<tr>
                        				<td>大图</td>
                        				<td><input type="file" name="burl" /> 130*130</td>
                        			</tr>
                        			<tr>
                        				<td></td>
                        				<td><input type="submit" value="提交" /></td>
                        			</tr>
                        		</table>
                            </div>
                            </form>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div>
</div>