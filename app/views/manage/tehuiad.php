<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> 
  <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
      <div class="question_nav">
          <ul>
              <li class="on"><a href="<?php echo base_url('manage/tehuiad')?>">特惠广告管理</a></li>
          </ul>
      </div>
      <div class="manage_yuyue">
        <div class="manage_yuyue_form">
          <ul> 
            <li style="width:10%">ID</li> 
            <li style="width:30%">图片</li>
            <li style="width:30%">url</li>                                   
            <li style="width:10%">操作</li>
            <div class="clear" style="clear:both;"></div>
          </ul>
          <?php 
              foreach($results as $row):
          ?>
					<ul>
            <li style="width:10%"><?php echo $row['id']?></li> 
            <li style="width:30%">
            <?php if(isset($row['banner_pic'])){ echo '<img style="width:50px;height:50px;" src="http://pic.meilimei.com.cn/upload/'.$row['banner_pic'].'"/>'; } ?></li>
            <li style="width:30%"><?php echo $row['url'];?></li> 
						<li style="width:10%">
            <a href="<?php echo site_url('manage/tehuiad/tehuiad_update/'.$row['id'])?>">编辑</a><li/>
          </ul>
					<?php endforeach; ?>
        </div>
      </div>
    
  </div>
  <div class="clear" style="clear:both;"></div>
</div>
