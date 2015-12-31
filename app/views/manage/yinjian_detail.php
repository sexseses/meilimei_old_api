<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">引荐人计划</a></li>
                            </ul>
                        </div>
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><?php echo form_open('manage/yinjian'); ?>
                            	<ul> 
                                    <li>名称<input name="sname" type="text" value="<?php echo $this->input->post('sname') ?>" maxlength="62"></li> 
                                    <li>手机号码<input name="phone" type="text" value="<?php echo $this->input->post('phone') ?>"></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li> 
                                </ul> </form>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li class="Vertical25">ID</li>
                                    <li style="width:30%">System</li>
                                    <li style="width:15%">IP</li>
                                    <li style="width:20%">标签</li><li class="Vertical28">时间</li>    .
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                              <?php 
								foreach($results as $row){
								  echo ' <ul>
                                	<li class="Vertical25">'.$row->id .'</li>
                                    <li style="width:30%">'.(trim($row->systype)!=''?str_replace('rv:18.0) Ge','Firefox',$row->systype):'未知') .'</li>
                                    <li style="width:15%">'.$row->ip .'</li><li style="width:20%">'.$row->ip .'</li>
                                    <li class="Vertical28">'.date('Y-m-d',$row->cdate).'</li> 
                                    <div class="clear" style="clear:both;"></div>
                                </ul>';	
								}							
								 ?>
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right">
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
