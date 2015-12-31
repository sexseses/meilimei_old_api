<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">数据导入管理</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                            <ul style="line-height:180%;text-indent:2em;padding:10px;font-size:20px;">
                                <li><a href="<?php echo site_url('manage/syncUserInfo') ?>" >最土用户数据同步</a><span style="font-size:12px;">(将美丽美MLzhensuo数据库的users表中的普通用户数据同步到最土特惠zuitu_db数据库b的user表中)</span></li>
                                <li><a href="<?php echo site_url('manage/importCoordinate') ?>">导入医院坐标</a></li>
                            </ul>

                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
