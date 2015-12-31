<div class="Personal_center_left">
    <h3><?php   $roleid = $this->wen_auth->get_role_id();if ($roleid == 1) {
        echo '会员账户';
        $tmp = $this->db->get_where('wen_notify', array('user_id' => $this->wen_auth->get_user_id()), 1)->result();
    } else if ($roleid == 2) {
        echo '医师账户';
    } else if ($this->session->userdata('admin_answer')) {

        echo '医师账户 (admin答疑)';
    } else if ($this->session->userdata('yiyuan_answer')) {
        $yishi_row=$this->db->query("select alias from users where id = {$this->session->userdata('yishi_id')}")->row_array();

        echo "医师账户<i style='font-size:12px;'>{$yishi_row['alias']}</i> (医院答疑)";
    } else {
        echo '医院账户';
    } ?> </h3>
    <ul> <?php if ($this->wen_auth->get_role_id() == 3) { ?>
        <li <?php echo $this->uri->segment(2) == 'myyishi' ? 'class="on4"' : ''; ?>><a
            href="http://www.meilimei.com/counselor/myyishi" class="item04">医师管理</a></li>  <?php } ?>
        <li>
            <a href="http://bd.meilimei.com/b_tehui/tehuilist" class="item01">特惠管理</a></li>
        <li>
            <a href="http://bd.meilimei.com/b_verification/CouponConfirm" class="item13">消费管理 </a></li>
		<?php
			if ($roleid != 1) {
        ?>
			<li <?php echo ($this->uri->segment(2) == 'yuyue') ? 'class="on3"' : ''; ?>><a
                href="<?php echo base_url() ?>counselor/yuyue" class="item03">客户记录</a></li>
        <?php } ?>
		
        <li <?php echo ($this->uri->segment(2) == 'info' || $this->uri->segment(2) == 'hetong' || $this->uri->segment(2) == 'zhengshu' || $this->uri->segment(2) == 'ablum') ? 'class="on2"' : ''; ?>>
            <a href="<?php echo base_url() ?>user/info" class="item02">修改资料</a></li>
        <li><a href="<?php echo base_url() ?>user/logout" class="item06">安全退出</a></li>
    </ul>
</div>