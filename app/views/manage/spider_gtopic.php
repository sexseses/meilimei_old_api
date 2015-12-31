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
                            	<li><a href="<?php echo site_url('manage/spider'); ?>">机构临时数据</a></li>
                                <li><a href="<?php echo site_url('manage/spider/topic'); ?>">话题临时数据</a></li>
                                <li><a href="<?php echo site_url('manage/spider/gjigou'); ?>">抓取机构</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/spider/gtopic'); ?>">抓取话题</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >   
                            <div id="GCtopic" class="comments"><?php echo form_open_multipart('manage/spider/gtopic'); ?> 
                            <ul style="padding:10px;"><li style="padding:10px;"><label style="width:100px; display:inline-block">地址*</label><input name="url" type="text"  style="padding:2px;" value="http://tieba.baidu.com/f?kw=%D5%FB%C8%DD&tp=0&pn=" size="45" /></li>   
                            
       <li style="padding:10px;"><label style="width:100px; display:inline-block">开始*</label><input name="starts" type="text"  style="padding:2px;" value="0" size="45" /></li>
        <li style="padding:10px;"><label style="width:100px; display:inline-block">结束*</label><input name="ends" type="text"  style="padding:2px;" value="50" size="45" /></li>           <li style="padding:10px;"><label style="width:100px; display:inline-block">每次增加*</label><input name="aplus" type="text"  style="padding:2px;" value="50" size="45" /></li>           
                           
                            <li style="padding:10px 10px 10px 100px;"><input id="spidsubmit" type="submit" name="submit" value="抓取" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div>
