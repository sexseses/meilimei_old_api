<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">推广管理</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search">
                            	<h5>搜素条件</h5><?php echo form_open('manage/tuiguang'); ?>
                            	<ul>
                                    <li>城市<input name="city" type="text" value="<?php echo $this->input->post('city') ?>"></li>
                                   <!-- <li>推广时间<input name="" type="text" value="2012-12-12"> — <input name="" type="text" value="2012-12-22"></li>-->
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li class="Vertical11">姓名</li> 
                                    <li class="Vertical13">联系方式</li>
                                    <li class="Vertical14">推广时间</li>
                                    <li class="Vertical15">推广地点</li>
                                    <li class="Vertical16">推广审核确定</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($results as $row){
									echo ' <ul>
                                	<li class="Vertical11">'.$row['alias'].'</li>
                                    <li class="Vertical12">'.$row['alias'].'</li>
                                    <li class="Vertical13">13568798523</li>
                                    <li class="Vertical14">'.data('Y-m-d',$row['startday']).' 至 '.data('Y-m-d',$row['endday']).' <br><strong>共'.(($row['endday']-$row['startday'])/3600*24).'天</strong></li>
                                    <li class="Vertical15">'.$row['city'].'</li> 
									 <li class="Vertical16" data-id="'.$row->id .'">'.($row->banned?'未开通':'<a>通过</a>').'</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>';
								}
								?>  
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" style="<?php echo $issubmit?'display:none':'' ?>">
                                    <ul>
                                        <li><a href="<?php echo $preview ?>" class="preview">&nbsp;</a></li>
                                        <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                    </ul>
                                    <h5>第<?php echo $offset ?>-<?php echo $offset+count($results)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                                </div>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
