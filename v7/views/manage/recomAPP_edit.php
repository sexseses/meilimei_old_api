<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg;
} ?>
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_shortcuts">
                <ul>
                    <li><a href="#">应用推荐管理</a></li>
                </ul>
            </div>
            <div class="tuijian_tab">
                <ul>
                    <a href="<?php echo site_url('manage/recomAPP/index'); ?>"> <li>应用列表</li></a>
                    <a href="<?php echo site_url('manage/recomAPP/edit'); ?>"><li class="on"><?php if(isset($row)): echo "修改应用"; else: echo "添加应用";endif; ?></li></a>
                </ul>
            </div>
            <div class="clear" style="clear:both;"></div>
            <div class="manage_yuyue">
                <div class="comments">
                    <form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <input name="id" type="hidden" value="<?php echo @$row['id']; ?>" >
                    <ul style="padding:10px;">
                        <li style="padding:10px;">
                            <label style="width:100px; display:inline-block">软件名称*</label>
                            <input name="name" type="text" style="padding:2px;" value="<?php echo @$row['name']; ?>" size="45">
                        </li>
                        <li style="padding:10px;">
                            <label style="width:100px; display:inline-block">缩略图*</label>
                            <input name="picture" type="file" size="45">
                        </li>
                        <li style="padding:10px;">
                            <label style="width:100px; display:inline-block">介绍*</label>
                            <textarea style="width:550px" name="content"><?php echo @$row['content']; ?></textarea>
                        </li>
                        <li style="padding:10px;">
                            <label style="width:100px; display:inline-block">下载链接*</label>
                            <input name="download" value="<?php echo @$row['download']; ?>" type="text" style="padding:2px;" size="45">
                        </li>
                        <li style="padding:10px 10px 10px 100px;">
                            <input type="submit" name="submit" value="<?php if(isset($row)): echo "修改"; else: echo "添加";endif; ?>" style="padding:2px 10px;">
                        </li>
                    </ul>
                </form>
                </div>
            </div>
        </div>

        <div class="clear" style="clear:both;"></div>
    </div>
</div>
