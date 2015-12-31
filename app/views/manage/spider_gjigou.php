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
                                <li class="on"><a href="<?php echo site_url('manage/spider/gjigou'); ?>">抓取机构</a></li>
                                <li><a href="<?php echo site_url('manage/spider/gtopic'); ?>">抓取话题</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >   
                            <div id="GCjigou" class="comments"><?php echo form_open_multipart('manage/spider/gjigou'); ?> 
                            <ul style="padding:10px;"><li style="padding:10px;"><label style="width:100px; display:inline-block">地址*</label><input name="url" type="text"  style="padding:2px;" value="http://www.dianping.com/search/category/1/50/g160r860p" size="45" /></li>   
        <li>http://www.dianping.com/search/category/1/50/g158r866p2 => http://www.dianping.com/search/category/1/50/g158r866p</li>                  
       <li style="padding:10px;"><label style="width:100px; display:inline-block">开始页数*</label><input name="starts" type="text"  style="padding:2px;" value="0" size="45" /></li>
        <li style="padding:10px;"><label style="width:100px; display:inline-block">结束页数*</label><input name="ends" type="text"  style="padding:2px;" value="1" size="45" /></li>                     
                           
                            <li style="padding:10px 10px 10px 100px;"><input id="spidsubmit" type="submit" name="submit" value="抓取" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                            </div><div id="GCjigoustate" style="display:none;padding:30px;">数据抓取中...</div>
                        </div>
                    </div><script>$(function(){
						$("#spidsubmit").click(function(){$('#GCjigou').hide();$('#GCjigoustate').show();});
					});</script>
    <div class="clear" style="clear:both;"></div>  
  </div>
</div>
