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
                    <li class="on"><a href="<?php echo base_url('manage/tehui')?>">特惠活动管理</a></li>
                    <li><a href="<?php echo base_url('manage/tehui/tehui_add')?>">添加特惠活动</a></li>
                </ul>
        	</div>

        	<div class="manage_yuyue">
        		<div class="manage_yuyue_form">
                    <table align="center">
                         <thead>
                            <tr>
                                <th width="5%">序号</th>
                                <th width="40%">活动名称</th>
                                <th width="10%">特惠id</th>
                                <th width="20%">关联的机构</th>
                                <th width="30%">操作</th>
                            </tr>
                        </thead>
                        <tbody align="center">
                            <?php foreach ($results as $value) { ?>
                                <tr>
                                    <td width="5%">
                                        <?php echo $value['id']; ?>
                                    </td>
                                    <td width="40%">
                                        <?php echo $value['name']; ?>
                                    </td>
                                    <td width="10%">
                                        <?php echo $value['tehui_id']; ?>
                                    </td>
                                    <td width="20%">
                                        <?php echo $value['cpname']; ?>
                                    </td>
                                    <td width="35%">
                                        <a onclick="return confirm(\'确认删除 ' . $row['title'] . '?\')" href="'. site_url('manage/tehui/del/' . $value['id']) .'">删除</a> 
                                        <a href="<?php echo base_url("manage/tehui/tehui_edit/{$value['id']}"); ?>">编辑</a>
                                        <a href="<?php echo base_url("manage/tehui/tehui_edit_mechanism/{$value['id']}"); ?>">机构</a>
                                        <a href="<?php echo base_url("manage/tehui/tehui_edit_physician/{$value['id']}"); ?>">医师</a>
                                        <a href="<?php echo base_url("manage/tehui/tehui_edit_product/{$value['id']}"); ?>">商品</a>
                                        <a href="<?php echo base_url("manage/tehui/tehui_item/{$value['id']}"); ?>">美人计</a>
                                        <a href="<?php echo base_url("manage/tehui/tehui_edit_items/{$value['id']}"); ?>">项目</a>
                                        <?php if($value['flashSale'] == 0){ ?>
                                            <a href="<?php echo base_url("manage/tehui/flashSale/{$value['id']}"); ?>">普</a>
                                        <?php }else{ ?>
                                            <a href="<?php echo base_url("manage/tehui/flashSale/{$value['id']}"); ?>">闪</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr height="10px"></tr>
                            <?php }  ?>
                        </tbody>
                    </table>
            	</div>
        	</div>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>

