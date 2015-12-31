<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css">
<div class="page_content932">
    <div class="institutions_info">
 	<?php  $this->load->view('manage/leftbar'); ?>
    <div class="Personal_center_right">
	    <div class="question_nav">
	        <ul>
	            <li <?php echo $this->uri->segment(3) == '' ? 'class="on"' : ''; ?>><a href="<?php echo site_url('manage/product_review') ?>" >在线预约产品列表</a></li>
	            <li <?php echo $this->uri->segment(3) == 'noreviewlist' ? 'class="on"' : ''; ?>><a href="<?php echo site_url('manage/product_review/noreviewlist') ?>" >未审核预约产品列表</a></li>
	            <li <?php echo $this->uri->segment(3) == 'reviewlist' ? 'class="on"' : ''; ?>><a href="<?php echo site_url('manage/product_review/reviewlist') ?>" >上架产品列表</a></li>
	        </ul>
	    </div>
    	<div class="question_list">
            <ul>
            <table align="center">
                <thead>
                    <tr>
                        <th width="5%">序号</th>
                        <th width="35%">活动名称</th>
                        <th width="25%">操作</th>
                    </tr>
                </thead>
                <tbody align="center">
                    <?php foreach ($team_rs as $value) { ?>
                    	<tr height="20px"></tr>
                        <tr>
                            <td width="5%">
                                <?php echo $value['id']; ?>
                            </td>
                            <td width="35%">
                                <?php echo $value['title']; ?>
                            </td>
                            <td width="25%">
                                <a onclick="return confirm(\'确认删除 ' . $value['title'] . '?\')" href="'. site_url('counselor/product_del/' . $value['id']) .">删除</a>
                                <?php if($value['review'] == 0){ ?>
                                <a href="<?php echo base_url("manage/product_review/review/{$value['id']}"); ?>">审核</a>
                                <?php }elseif($value['review'] == 1){ ?>
                                <a href="#">已审核</a>
                                <?php }else{ ?>
                                <a href="#">未通过</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }  ?>
                </tbody>
            </table>
            </ul>
        </div>
        <?php echo $pagelink; ?>
    </div>
    <div class="clear" style="clear:both;"></div>
    </div>
</div>
