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
                    <li class="on"><a href="<?php echo base_url('manage/flashsaletop')?>">闪购广告管理</a></li>
                    <li><a href="<?php echo base_url('manage/flashsaletop/add')?>">添加闪购广告</a></li>
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
                            <th width="10%">状态</th>
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
                                <td width="30%">
                                    <img src="<?php echo $value['banner']; ?>"  width="30%" >
                                </td>

                                <td width="10%">
                                    <?php echo $value['state']; ?>
                                </td>
                                <td width="25%">
                                    <a onclick="return confirm(\'确认删除 '  . $value['title'] . '?\')" href="<?php echo base_url("manage/flashSaletop/del/{$value['topid']}"); ?>">删除</a>
                                    <a href="<?php echo base_url("manage/flashSaletop/edit/{$value['topid']}"); ?>">修改商品</a>
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
