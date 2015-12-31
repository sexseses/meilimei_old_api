<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
        <div class="question_nav">
            <ul>
                <li class="on"><a href="<?php echo base_url('manage/item/')?>">项目管理</a></li>
                <li><a href="<?php echo base_url('manage/item/item_ badd/'.$pid)?>">添加项目</a></li>
            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form">
                        		<table >
                        			<tr>
                        				<th style="width:10%">ID</th>
                        				<th style="width:10%">PID</th>
                        				<th style="width:35%">名称</th>
                        				<th style="width:20%">操作</th>
                        				<th style="width:20%"></th>
                        			</tr>
									<?php foreach($results as $row){ ?>
										<tr>
											<td style="width:10%;text-align:center"><?php echo $row['id'] ?></td>
											<td style="width:10%;text-align:center"><?php echo $row['pid'] ?></td>
											<td style="width:35%;text-align:center"><?php echo $row['name'] ?></td>
											<td style="width:20%;text-align:center"><a href="item/item_add/<?php echo $row['pid'] ?>/<?php echo $row['id'] ?>" >添加</a>  <a href="item/item_add/<?php echo $row['id'] ?>" >编辑</a>  <a href="item/item_del/<?php echo $row['id'] ?>" >删除</a></td>
											<td style="width:20%;text-align:center"><input type="button"  value="显示子项目" onclick = "displayItem(<?php echo $row['id'] ?>)"></td>
										</tr>
										<tr>
											<td colspan="5">
												<table style="width:100%;text-align:center;display:none;" class="childItem<?php echo $row['id']; ?>">
													<tr>
														<th style="width:10%"></th>
	                        							<th style="width:10%">ID</th>
	                        							<!-- <th style="width:10%">PID</th> -->
	                        							<th style="width:35%">名称</th>
	                        							<th style="width:20%">操作</th>
	                        						</tr>
	                        						<?php foreach($row['child'] as $child){ ?>
														<tr>
															<td style="width:10%;text-align:center"> </td>
															<td style="width:10%;text-align:center"><?php echo $child['id'] ?></td>
															<!-- <td style="width:10%;text-align:center"><?php //echo $child['pid'] ?></td> -->
															<td style="width:35%;text-align:center"><?php echo $child['name'] ?></td>
															<td style="width:30%;text-align:center"><a href="item/item_add/<?php echo $child['pid'] ?>/<?php echo $child['id'] ?>" >添加</a>  <a href="item/item_add/<?php echo $child['id'] ?>" >编辑</a>  <a href="item/item_del/<?php echo $child['id'] ?>" >删除</a></td>
															<td style="width:20%;text-align:center"><input type="button" id='displayitem' value="显示子项目"></td>
														</tr>
													<?php } ?>
												</table>
											</td>
										</tr>
									<?php } ?>
                        		</table>
                        	</div>
						</div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div>
</div>
<style>
 
</style>
<script type="text/javascript">
function displayItem(childid){
	$(".childItem"+childid).toggle();
}
</script>