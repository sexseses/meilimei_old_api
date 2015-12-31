<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li class="on"><a href="<?php echo base_url('manage/onetehui')?>">一元活动管理</a></li>
                    <li><a href="<?php echo base_url('manage/onetehui/onetehui_add')?>">添加活动</a></li>
                </ul>
        	</div>

        	<div class="manage_yuyue">
        		<div class="manage_yuyue_form">
                    <table align="center">
                         <thead>
                            <tr>
                                <th width="5%">序号</th>
                                <th width="20%">活动名称</th>
                                <th width="20%">开始日期</th>
                                <th width="20%">结束日期</th>
                                <th width="10%">操作</th>
                            </tr>
                        </thead>
                        <tbody align="center">
                            <?php foreach ($results as $value) { ?>
                                <tr>
                                    <td width="5%">
                                        <?php echo $value['id']; ?>
                                    </td>
                                    <td width="20%">
                                        <?php echo $value['name']; ?>
                                    </td>
                                    <td width="20%">
                                        <?php echo date('Y-m-d',$value['begin_time']); ?>
                                    </td>
                                    <td width="20%">
                                        <?php echo date('Y-m-d',$value['end_time']); ?>
                                    </td>
                                    <td width="15%">
                                        <a onclick="return confirm(\'确认删除 ' . $row['title'] . '?\')" href="' . site_url('manage/yiyuanevent/del/' . $value['id']) . '">删除</a> 
                                        <a href="<?php echo base_url("manage/onetehui/onetehui_edit/{$value['id']}"); ?>">编辑</a>
                                        <a href="<?php echo base_url("manage/onetehui/onetehui_edit_mechanism/{$value['id']}"); ?>">编辑机构</a>
                                        <a href="<?php echo base_url("manage/onetehui/onetehui_edit_physician/{$value['id']}"); ?>">编辑医师</a>
                                        <a href="<?php echo base_url("manage/onetehui/onetehui_edit_product/{$value['id']}"); ?>">关联产品</a>
                                        <a href="<?php echo base_url("manage/onetehui/onetehui_item/{$value['id']}"); ?>">关联美人计</a>
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

