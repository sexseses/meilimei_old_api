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
                    <li class="on"><a href="<?php echo base_url('manage/flashSale')?>">闪购管理</a></li>
                    <li><a href="<?php echo base_url('manage/flashSale/add')?>">添加闪购活动</a></li>
                    <li><a href="<?php echo base_url('manage/flashSale/IndexTopBanner')?>">首页置顶活动</a></li>
                </ul>
        	</div>

        	<div class="manage_yuyue">
        		<div class="manage_yuyue_form">
                    <table align="center">
                         <thead>
                            <tr>
                                <th width="5%">序号</th>
                                <th width="20%">活动名称</th>
                                <th width="15%">banner图</th>
                                <th width="15%">列表图</th>
                                <th width="15%">发布时间</th>
                                <th width="10%">等级</th>
                                <th width="10%">操作</th>
                            </tr>
                        </thead>
                        <tbody align="center">
                            <?php foreach ($sale_rs as $value) { ?>
                                <tr>
                                    <td width="5%">
                                        <?php echo $value['id']; ?>
                                    </td>
                                    <td width="20%">
                                        <?php echo $value['title']; ?>
                                    </td>
                                    <td width="15%">
                                        <img src="<?php echo $value['banner']; ?>"  width="30%" height="30%"/>
                                    </td>
                                    <td width="15%">
                                        <img src="<?php echo $value['lbanner']; ?>"  width="30%" height="30%"/>
                                    </td>
                                    <td width="15%">
                                        <?php echo date('Y-m-d',$value['begin']); ?>
                                    </td>
                                    <td width="10%">
                                        <?php echo $value['level']; ?>
                                    </td>
                                    <td width="25%">
                                        <a onclick="return confirm(\'确认删除 '  . $value['title'] . '?\')" href="<?php echo base_url("manage/flashSale/del/{$value['id']}"); ?>">删除</a> 
                                        <a href="<?php echo base_url("manage/flashSale/edit/{$value['id']}"); ?>">编辑</a>
                                        <a href="<?php echo base_url("manage/flashSale/edit_banner/{$value['id']}"); ?>">添加图片</a>
                                        <a href="<?php echo base_url("manage/flashSale/add_product/{$value['id']}"); ?>">添加商品</a>
                                        <a href="<?php echo base_url("manage/flashSale/new_edit_product/{$value['id']}"); ?>">修改商品</a>
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
