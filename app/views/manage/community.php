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
                    <li class="on"><a href="<?php echo base_url('manage/community')?>">社区活动管理</a></li>
                    <li><a href="<?php echo base_url('manage/community/add')?>">添加社区活动</a></li>
                   
                </ul>
        	</div>

        	<div class="manage_yuyue">
        		<div class="manage_yuyue_form">
                    <table align="center">
                         <thead>
                            <tr>
                                <th width="5%">序号</th>
                                <th width="15%">活动标题</th>
                                <th width="10%">活动类型</th>
                                <th width="10%">美豆数量</th>
                                <th width="10%">banner图</th>
                                <th width="15%">活动时间</th>
                                <th width="15%">结束时间</th>
                                <th width="*">操作</th>
                            </tr>
                        </thead>
                        <tbody align="center">
                            <?php foreach ($event_rs as $value) { ?>
                                <tr>
                                    <td width="5%">
                                        <?php echo $value['id']; ?>
                                    </td>
                                    <td width="15%">
                                        <?php echo $value['event_title']; ?>
                                    </td>
                                    <td width="15%">
                                        <?php
                                            if($value['event_type'] == 'baoming'){
                                                $controller = "baomingCollection";
                                                echo  "报名";
                                            }else{
                                                $controller = "fatieCollection";
                                                echo  "发帖";
                                            }
                                        ?>
                                    </td>
                                    <td width="5%">
                                        <?php echo $value['event_score']; ?>
                                    </td>
                                    <td width="10%">
                                        <img src="<?php echo $this->remote->show($value['event_pic']) ?>"  width="30%" height="30%"/>
                                    </td>
                                    <td width="15%">
                                         <?php echo date('Y-m-d',$value['begin_time']); ?>
                                    </td>
                                    <td width="15%">
                                        <?php echo date('Y-m-d',$value['end_time']); ?>
                                    </td>
                                    <td width="25%">
                                        <a onclick="return confirm(\'确认删除 ' . $value['event_title'] . '?\')" href="<?php echo base_url("manage/community/del/{$value['id']}"); ?>">删除</a> 
                                        <a href="<?php echo base_url("manage/community/edit/{$value['id']}"); ?>">编辑</a>
                                        <a href="<?php echo base_url("manage/community/edit_banner/{$value['id']}"); ?>">图片</a>
                                        <a href="<?php echo base_url("manage/community/{$controller}/{$value['id']}"); ?>">详情</a>
                                    </td>
                                </tr>
                            <?php }  ?>
                        </tbody>
                    </table>
            	</div>
        	</div>
			<div class="paging">
                                <div class="paging_right" >
                                   <?php echo $pagelink ?>
                                </div>
                            </div>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>
