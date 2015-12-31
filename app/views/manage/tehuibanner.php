<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 

<div class="page_content937">
	<div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
		<div class="manage_center_right">
            <div class="manage_yuyue">
                <div class="manage_yuyue_form">
                    <ul> 
						<li style="width:10%">ID</li> 
                        <li style="width:10%">类别</li> 
                        <li style="width:30%">图片</li>
                        <li style="width:30%">url</li>                                   
                        <li style="width:10%">操作</li>
                        <div class="clear" style="clear:both;"></div>
                    </ul>
                        <?php 
                            foreach($results as $row):
                                $type='';
                                if(isset($row['type'])&&$row['type']=='1'){
                                $type ='新人专享';
                            }elseif(isset($row['type'])&&$row['type']=='2'){
                                $type ='一元特惠 ';
                            }elseif(isset($row['type'])&&$row['type']=='3'){
                                $type ='积分特惠';
                            }
                        ?>
					<ul>
                        <li style="width:10%"><?php echo $row['id']?></li> 
                        <li style="width:10%"><?php echo $type;?></li> 
                        <li style="width:30%"><?php if(isset($row['banner_pic'])){ echo '<img style="width:50px;height:50px;" src="http://pic.meilimei.com.cn/upload/'.$row['banner_pic'].'"/>'; } ?></li>
                        <li style="width:30%"><?php echo $row['url'];?></li> 
						<li style="width:10%"><a href="<?php echo site_url('manage/tehuibanner/addimg?id='.$row['id'])?>">编辑</a><li/>
                        <div class="clear" style="clear:both;"></div>
                    </ul>
					<?php endforeach;?>
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
            <div style="float:right">
	            <input type="button" value="添加" onclick="parent.location.href='<?php echo site_url('manage/tehuibanner/addimg')?>'" />
            </div>
		</div>
	<!--banner活动开始-->
		<div class="page_content937">
			<div class="manage_center_right">
                <div class="question_nav">
                    <ul>
                        <li class="on"><a href="<?php echo site_url('manage/tehuibanner')?>">Banner管理</a></li>
						<li><a href="<?php echo site_url('manage/tehuibanner/topadd')?>">添加Banner</a></li>
                    </ul>
                </div>
                <div class="manage_yuyue" >
                    <div class="">
						<?php echo form_open_multipart('manage/tehuibanner'); ?> 
							<ul> 
								<li>
									地区
									<select name="sname">
										<option value="">全部</option>
										<?php foreach($results3 as $row){
											echo '
												<option value="@'.$row['id'].'@">'.$row['name'].'</option>
											';
										} ?>	
									</select>
									<input type="submit" value="搜索" class="search"/>
								</li>  
                            </ul>
						<?php echo form_close(); ?>
                    </div> 
                    <div class="manage_yuyue_form">
                        <ul>
                            <li style="width:5%">ID</li> 
                            <li style="width:15%">标题</li>
                            <li style="width:20%">图片</li>
                            <li style="width:15%">链接</li> 
							<li style="width:15%">日期</li> 
                            <li style="width:10%">操作</li>
                            <div class="clear" style="clear:both;"></div>
                        </ul> 
                    <?php 
						foreach($results2 as $row){
							echo ' 
								<ul>
                                	<li style="width:5%">'.$row['id'].'</li>
									<li style="width:15%">'.$row['btitle'].'</li>
                                    <li style="width:20%">
										<img style="max-height:100px; max-width:100px" src="http://pic.meilimei.com.cn/upload/'.$row['bimg'].'"/>
									</li> 
									<li style="width:15%">'.$row['burl'].'</li>
                                    <li style="width:15%">'.date('Y-m-d',$row['stday']).'</li>  
								    <li style="width:10%">
										<a onclick="return confirm(\'确认删除 '.$row->title.'?\')" href="'.base_url('manage/tehuibanner/topdel?id='.$row['id']).'">
											删除
										</a>  
										<a href="'.base_url('manage/tehuibanner/topedit?id='.$row['id']).'">
											编辑
										</a>
									</li>
									 <div class="clear" style="clear:both;"></div>
                                </ul>
							';	
						}							
					?>
                        <div class="clear" style="clear:both;"></div>
                    </div>
                    <div class="paging">
                        <div class="paging_right" style="<?php echo $issubmit?'display:none':'' ?>">
                            <ul>
                                <li>
									<a href="<?php echo $preview ?>" class="preview">&nbsp;</a>
								</li>
								<li>
									<a href="<?php echo $next ?>" class="next">&nbsp;</a>
								</li>
                            </ul>
                            <h5>第<?php echo $offset ?>-<?php echo $offset+count($results)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                        </div>
                    </div>
                </div>
            </div><script type="text/javascript">                   
			<div class="clear" style="clear:both;"></div>
		</div>
	</div>
</div>