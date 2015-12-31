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
                                    <li>医生(医院)名称<input name="sname" type="text" value="<?php echo $this->input->post('sname') ?>" maxlength="62"></li> 
                                    <li>手机号码<input name="phone" type="text" value="<?php echo $this->input->post('phone') ?>"></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li> 
                                </ul> </form>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li class="Vertical25">用户名</li>
                                    <li class="Vertical26">Email</li>
                                    <li class="Vertical27">手机</li>
                                    <li class="Vertical28">引荐进入网址</li>
                                    <li class="Vertical29">引荐下载总次数</li>
                                    <li class="Vertical30">详情</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                              <?php 
								foreach($results as $row){
									$this->db->select('alias, email, phone');
									$this->db->where('coupon_code', $row->coupon_code);
									$this->db->from('users');
									$tmp  = $this->db->get()->result(); 
								  echo ' <ul>
                                	<li class="Vertical25">'.$tmp[0]->alias .'</li>
                                    <li class="Vertical26">'.$tmp[0]->email .'</li>
                                    <li class="Vertical27">'.$tmp[0]->phone .'</li>
                                    <li class="Vertical28">'.$row->url .'</li>
                                    <li class="Vertical29">'.$row->nums.'</li> 
									<li class="Vertical29"><a href="'.site_url('manage/yinjian/detail/'.$row->coupon_code).'">查看</a></li> 
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
