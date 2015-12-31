<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 

<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><form id="userform" accept-charset="utf-8" method="get" action="<?php echo site_url('manage/gbos') ?>"> 
                            	<ul>
                                    <li>下单时间<input style="width:20px;" type="checkbox" name="opendate" <?php echo $this->input->get('opendate')?'checked="checked"':'' ?> value="1" />
                                    <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li>
                                    <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $edate; ?>" class="datepicker"></li>
                                    <li>手机号码<input name="phone" type="text" value="<?php echo $this->input->get('phone') ?>"></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form><?php echo isset($total_rows)?$total_rows:'' ?>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:7%">ID</li> 
                                    <li style="width:10%">名字</li> 
                                    <li style="width:20%">手机</li>
                                    <li style="width:10%">城市</li>
                                    <li style="width:40%">照片</li>
                                    <li style="width:10%">改善选项</li>
                                </ul>
                                  <?php 
                                    if(isset($results)){
                                      foreach($results as $row){
                                  ?>
                                  <ul class="">
                                	 <li style="width:7%"><?php echo $row->gbosid; ?></li>
                                     <li style="width:10%"><?php echo $row->name; ?></li>
                                     <li style="width:20%"><?php echo $row->mobile; ?></li>
                                     <li style="width:10%"><?php echo $row->city; ?></li>
                                     <li style="width:40%;height:60%"><div>
                                      <?php
                                      $images = explode(',', $row->images);
                                        if(count($images) > 0){
                                          foreach ($images as $value) {
                                            $img = "http://pic.meilimei.com.cn/upload".$value;
                                            echo "<img src='$img' style='width:30%;height:90%'  />";
                                          }
                                        }else{
                                          echo "没有图片！";
                                        }
                                      ?></div>
                                    </li>
                                     <li style="width:10%"><?php echo $row->tag; ?></li>
                                    </ul>
                                <?php
                                  }
                                }
                                ?>
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" >
                                   <?php echo isset($pagelink) ?$pagelink:''?>
                                </div>
                            </div>
                        </div>
  </div>
  <div class="clear" style="clear:both;"></div>
</div>
