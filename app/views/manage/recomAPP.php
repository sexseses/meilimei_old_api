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
                    <a href="<?php echo site_url('manage/recomAPP/index'); ?>"> <li class="on">应用列表</li></a>
                    <a href="<?php echo site_url('manage/recomAPP/edit'); ?>"><li>添加应用</li></a>
                </ul>
            </div>
            <div class="clear" style="clear:both;"></div>
            <div class="manage_yuyue">
                <div class="manage_search">
                    <form id="userform" accept-charset="utf-8" method="get"
                          action="<?php echo site_url('manage/recomAPP') ?>">
                        <ul>
                            <li>关键字<input name="keywords" type="text" value="" maxlength="62"></li>

                            <li><input name="submit" type="submit" value="搜索" class="search"></li>
                        </ul>
                    </form>
                </div>
                <div class="manage_yuyue_form">
                    <ul>
                        <li style="width:10%">软件名称</li>
                        <li style="width:10%">图片</li>
                        <li style="width:15%">下载地址</li>
                        <li class="Vertical23" style="width:30%">详细</li>
                        <li class="Vertical24" style="width:10%">添加时间</li>
                        <li class="width:15%">操作</li>
                        <div class="clear" style="clear:both;"></div>
                    </ul>

                    <?php foreach($result as $k=>$v): ?>
                    <ul>
                        <li style="width:10%"><?php echo $v['name']; ?></li>
                        <li style="width:10%">
                            <img src="<?php echo base_url().$v['picture']; ?>" style="max-width:100px;max-height: 50px;" >
                        </li>
                        <li style="width:15%;word-break: break-all;"><?php echo $v['download']; ?></li>
                        <li class="Vertical23" style="width:30%"><?php echo $v['content']; ?></li>
                        <li class="Vertical24" style="width:10%"><?php echo date('Y-m-d',$v['ctime']); ?></li>
                        <li class="width:15%">
                            <a href="<?php echo site_url('manage/recomAPP/edit')."?id=".$v['id']; ?>">修改</a>
                            <a href="<?php echo site_url('manage/recomAPP/del')."?id=".$v['id']; ?>">删除</a>
                        </li>
                        <div class="clear" style="clear:both;"></div>
                    </ul>
                    <?php endforeach; ?>

                    <div class="clear" style="clear:both;"></div>
                </div>
                <div class="paging">
                    <div class="paging_right">

                    </div>
                </div>
            </div>
        </div>

        <div class="clear" style="clear:both;"></div>
    </div>
</div>
