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
                        	<div class="manage_yuyue_form"><?php echo form_open_multipart(); ?>
                            	<dl>
	                                <dd>
	                                	<label>名称</label>
	                                	<input type="text" value="<?php echo $results[0]->name ?>" name="name" />
	                                </dd>
	                                <dd>
	                                	<label>小图</label>
	                                	<input type="file" name="surl" />  90*90
	                                </dd>
	                                <dd style="height:90px;">
	                                	<img width="90" height="90" src="<?php echo site_url().'upload/'.$results[0]->surl ?>" />
	                                </dd>


	                                <dd>
	                                	<label>大图</label>
	                                	<input type="file" name="burl" /> 130*130
	                                </dd>
	                                <dd style="height:130px;">
	                                	<img width="130" height="130" src="<?php echo site_url().'upload/'.$results[0]->burl ?>" />
	                                </dd>
	                                
	                                	<input type="hidden" name="ssurl" value="<?php echo $results[0]->surl ?>" />
	                                	<input type="hidden" name="sburl" value="<?php echo $results[0]->burl ?>" />

                            	</dl>
                            </div>
                            </form>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div>
</div>
