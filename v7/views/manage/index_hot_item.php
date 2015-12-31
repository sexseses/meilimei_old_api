<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('manage/index_hot_item'); ?>">热门项目管理</a></li>
                                <li><a href="<?php echo site_url('manage/index_hot_item/itemadd'); ?>">添加项目</a></li>
                            </ul>
                        </div>
                        <!--  
                        <div class="manage_search"><form method="get" action="<?php echo base_url('manage/diary'); ?>">
                                <ul>
                                    <li>关键词<input name="sname" type="text" value="<?php echo $this->input->get('sname') ?>"></li>
                                    <li>类型<select name="types"><option value="">全部</option><option <?php echo $this->input->get('types')=='1'?'selected="selected""':'' ?> value="1">windows</option><option <?php echo $this->input->get('types')=='0'?'selected="selected""':'' ?> value="0">非 windows</option></select></li>
                                    <li>标签<input type="text" name="tags" value="<?php echo $this->input->get('tags');?>"/></li>
                                    <li>用户名<input type="text" name="username" value="<?php echo $this->input->get('username');?>"/></li>
                                    <li>启动<select name="loading"><option value="">全部</option><option <?php echo $this->input->get('loading')=='1'?'selected="selected""':'' ?>  value="1">是</option><option <?php echo $this->input->get('loading')==='0'?'selected="selected""':'' ?> value="0">否</option></select></li>
                                    <li>推荐<select name="is_front"><option value="">全部</option><option <?php echo $this->input->get('is_front')=='1'?'selected="selected""':'' ?>  value="1">是</option><option <?php echo $this->input->get('is_front')==='0'?'selected="selected""':'' ?> value="0">否</option></select></li>
                                    <li>开始时间:<input type="text" value="" name="stime" id="stime" class="datepicker" /> 结束时间:<input type="text" value="" name="etime" id="etime" class="datepicker" /></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form>
                        </div>
                        -->
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:10%">编号</li>
                                    <li style="width:30%">项目名</li>
                                    <li style="width:20%">level</li>
                                    <li style="width:20%">时间</li>
                                    <li class="width:20%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php
								foreach($results as $row){

								  echo ' <ul class="">
                                	 <li style="width:10%">'.$row['id'] .'</li>
                                	 <li style="width:30%">'.$row['item_name'] .'</li>
                                     <li style="width:20%">'.$row['level'].'</li>
                                    <li style="width:20%">
                        	               '.date('Y-m-d', $row['create_time']).'
                                    </li>
								    <li class="width:20%">
                                    <a  href="'.site_url('manage/index_hot_item/itemadd/'.$row['id']).'">
                                        编辑
                                    </a>&nbsp;&nbsp;
                                    <a onclick="return confirm(\'确认删除 '.$row->nid.'?\')" href="'.site_url('manage/index_hot_item/del/'.$row['id']).'">
                                        删除
                                    </a>
                                    </li> <div class="clear" style="clear:both;"></div>
                                </ul>';	
								}							
								 ?>
                            </div>
                            <div class="paging">
                                <div class="paging_right" >
                                   <?php echo $pagelink ?>
                                </div>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
