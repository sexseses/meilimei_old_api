<?php  
if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} 
?> 
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li class="on"><a href="<?php echo base_url('manage/event')?>">活动管理</a></li>
                    <li><a href="<?php echo base_url('manage/event/add')?>">添加活动</a></li>
                </ul>
        	</div>

        	<div class="manage_yuyue">
        		<div class="manage_yuyue_form">
                    <table align="center">
                         <thead>
                            <tr>
                                <th width="5%">序号</th>
                                <th width="30%">活动名称</th>
                                <th width="10%">开始时间</th>
                                <th width="10%">结束时间</th>
                                <th width="40%">操作</th>
                            </tr>
                        </thead>
                        <tbody align="center">
                            <?php foreach ($event_rs as $value) { ?>
                                <tr>
                                    <td width="5%">
                                        <?php echo $value['id']; ?>
                                    </td>
                                    <td width="30%">
                                        <?php echo $value['event_name']; ?>
                                    </td>
                                    <td width="10%">
                                        <?php echo date('Y-m-d', $value['begin_time']); ?>
                                    </td>
                                    <td width="10%">
                                        <?php echo date('Y-m-d', $value['end_time']); ?>
                                    </td>
                                    <td width="40%">
                                        <a onclick="return confirm(\'确认删除 ' . $row['title'] . '?\')" href="<?php echo base_url("manage/event/del/{$value['id']}"); ?>">删除</a> 
                                        <a href="<?php echo base_url("manage/event/edit/{$value['id']}"); ?>">编辑</a>
                                        <a href="<?php echo base_url("manage/event/collection/{$value['id']}"); ?>">详细信息</a>
                                    </td>
                                </tr>
                            <?php }  ?>
                        </tbody>
                    </table>
            	</div>
        	</div>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>

