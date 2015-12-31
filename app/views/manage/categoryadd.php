<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo base_url('manage/category/')?>">项目管理</a></li><li class="on"><a>添加项目</a></li>
                            </ul>
                        </div> <style type="text/css">dd{display:block;line-height:30px;height:30px;margin-bottom:5px;}</style>
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form"><?php echo form_open_multipart(); ?> <form method="post" enctype="multipart/form-data">
                            	<dl>
                                <dd><label>名称</label><input type="text" value="" name="name" /></dd>
                                <dd><label>价格</label><input type="text" value="" name="price" /></dd>
                                <dd style="height:60px"><label>描述</label><textarea style="height:50px;width:300px;" name="des"></textarea></dd>
                                <dd><label>栏目图</label><input type="file" name="surl" /> 90*90</dd>
                                <dd><label>缩略图</label><input type="file" name="burl" /> 130*130</dd>
                                <dd><label>热门</label><input type="checkbox" value="1" name="is_hot" /></dd>
                                <dd><label>首页默认项目</label><input type="checkbox" value="1" name="is_default" /></dd>
                                <dd><label>排序</label><input type="text" value="" name="order" /></dd>
                                <dd><button style="width:100px" type="submit">提交</button></dd>
                                </dl></form>
                                <div class="clear" style="clear:both;"></div>
                                
                            </div>
                             
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div>
</div>
