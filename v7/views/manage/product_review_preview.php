<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<link charset="utf-8" type="text/css" href="http://tehui.meilimei.com/static/css/index.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css">
<div class="page_content932">
    <div class="institutions_info">
 	<?php  $this->load->view('manage/leftbar'); ?>
    <div class="Personal_center_right">
	    <div class="question_nav">
	        <ul>
	            <li class="on"><a href="<?php echo site_url('manage/product_review') ?>" >在线预约产品列表</a></li>
	            <li><a href="<?php echo site_url('manage/product_review/noreviewlist') ?>" >未审核预约产品列表</a></li>
	            <li><a href="<?php echo site_url('manage/product_review/reviewlist') ?>" >上架产品列表</a></li>
	        </ul>
	    </div>
    	<div class="question_list">
            <ul>
            <table align="center" >
                        <table >
                            <tbody>
                                <tr><td><h1>团购：<?php echo $team_rs['title']; ?></h1></td></tr>
                                <tr><td>原价: <?php echo $team_rs['market_price']; ?></td></tr>
                                <tr><td>预约价: <?php echo $team_rs['team_price']; ?></td></tr>
                                <tr><td>定金: <?php echo $team_rs['deposit']; ?></td></tr>
                                <tr><td>时间: <?php echo date("Y-m-d",$team_rs['begin_time']); ?>到<?php echo date("Y-m-d",$team_rs['end_time']);  ?></td></tr>
                                <tr><td><img width="400" height="200" src="http://tehui.meilimei.com/static/<?php echo $team_rs['image']; ?>"></td></tr>
                                <tr><td>本单详情</td></tr>
                                <tr><td><?php echo $team_rs['detail']; ?></td></tr>
                                <tr><td>特别提示</td></tr>
                                <tr><td><?php echo $team_rs['notice']; ?></td></tr>
                                <tr><td>审核</td></tr>
                                <tr><td>
                                <form action="/manage/product_review/review" method="post">
                                    <textarea  id="team-create-notice" name="no_review" rows="5" cols="45" ></textarea>
                                    <input type="submit" value="不通过">
                                    <input type="hidden" name='act' value="noreview">
                                    <input type="hidden" name='id' value="<?php echo $id; ?>">
                                </form>
                                <form action="/manage/product_review/review" method="post">
                                    <input type="submit"  value="通过" >
                                    <input type="hidden" name='act' value="review">
                                    <input type="hidden" name='id' value="<?php echo $id; ?>">
                                </form>
                                </td></tr>
                            </tbody>
                        </table>
            </table>
            </ul>   
        </div>
    </div>
    <div class="clear" style="clear:both;"></div>
    </div>
</div>
