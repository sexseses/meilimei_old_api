<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg; 
} ?> 
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css?v=112">
<script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function (K) {
        window.editor = K.create('#team-create-detail', {
                        items : [
        'source', '|', 'undo', 'redo', '|', 'preview', 
        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull',
         'clearhtml', 'quickformat', 'selectall', '/', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 
        'table', 'hr', 'baidumap', 
        'link', 'unlink' 
        ]
     });});
    KindEditor.ready(function (K) {
        window.editor = K.create('#team-create-notice', {
                        items : [
        'source', '|', 'undo', 'redo', '|', 'preview', 
        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull',
         'clearhtml', 'quickformat', 'selectall', '/', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 
        'table', 'hr', 'baidumap', 
        'link', 'unlink' 
        ]
     }); 
 
    });
</script>
<style type="text/css">

</style>
<div class="page_content932">
    <div class="institutions_info">
 	<?php $this->load->view('theme/include/dashboard'); ?>
    <div class="Personal_center_right">
	    <div class="question_nav">
	        <ul>
	            <li><a href="<?php echo site_url('counselor/product') ?>" >在线预约产品列表</a></li>
	            <li><a href="<?php echo site_url('counselor/product_add') ?>" >添加在线预约产品</a></li>
                <li class="on"><a href="#" >修改在线预约产品</a></li>
	        </ul>
	    </div>
        <form action="counselor/product_add?act=edit" method="post" id="addform">
        <input type='hidden' value="<?php echo $id; ?>" name='id'>
    	<div class="product_add">
            <table class="product_add_table">
                <tr>
                    <td width="30%">项目类型: </td>
                    <td width="70%">
                    <select id="sub_id" name="sub_id">
                        <option value="0">选择细分类</option> 
                        <?php
                            foreach ($group_rs as $group_v) {
                        ?>
                        <option <?php if($team_rs['sub_id']==$group_v['id']){ ?> checked="checked" <?php } ?> value="<?php echo $group_v['id']  ?>"><?php echo $group_v['name'] ?></option> 
                        <?php
                             } 
                        ?>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td width="30%">项目城市: <input type="hidden" name="city_id" value="<?php echo $team_rs['city_id']; ?>"></td>
                    <td width="70%">
                        <input type="checkbox"   value="0" name="city_ids[]" id="city_all">全部
                        <?php
                            foreach ($city_rs as $city_v) {
                        ?>
                        <input type="checkbox" <?php if(true){ ?> <?php } ?> value="<?php echo $city_v['id']; ?>" name="city_ids[]" ><?php echo $city_v['name']; ?>
                        <?php
                             } 
                        ?>
                    </td>
                </tr>
                <!-- <tr>
                    <td width="30%">项目标签: <input type="hidden" name="city_id" value="<?php echo $team_rs['city_id']; ?>"></td>
                    <td>
                    <span>点击添加</span>
                    <div class="itemdiv">sss</div>
                    </td>
                </tr> -->
                <tr>
                    <td width="30%">地区类型: </td>
                    <td width="70%">
                        <select name="areatype">
                            <option value="0">特定区域</option> 
                            <option value="1">全部城市</option> 
                    </select>
                    </td>
                </tr>
                <tr>
                    <td width="30%">项目标签: </td>
                    <td width="70%">
                         <input type="text" value="None"  id="team-create-tags" name="tags" size="30" value="<?php echo $team_rs['tags']; ?>">
                    </td>
                </tr>
                <tr>
                    <td width="30%">限制条件: </td>
                    <td width="70%">
                        <select style="width:160px;"  name="conduser">
                            <option <?php if($team_rs['conduer'] == Y){ ?> checked="checked" <?php } ?> value="Y">以购买成功人数成团</option>
                            <option <?php if($team_rs['conduer'] == N){ ?> checked="checked" <?php } ?> value="N">以产品购买数量成团</option>
                        </select>
                        <select style="width:160px;"  name="buyonce">
                            <option <?php if($team_rs['buyonce'] == Y){ ?> checked="checked"  <?php } ?> value="Y">仅购买一次</option>
                            <option <?php if($team_rs['buyonce'] == N){ ?> checked="checked"  <?php } ?> value="N">可购买多次</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="30%">项目标题: </td>
                    <td width="70%">
                         <input type="text"   value="<?php echo $team_rs['title'] ?>"  id="team-create-title" name="title" size="30" lastvalue="">
                    </td>
                </tr>
                <tr>
                    <td width="30%">价格: </td>
                    <td width="70%">
                        <label>市场价</label>
                        <input type="text" value="<?php echo $team_rs['market_price'] ?>" class="number" id="team-create-market-price" name="market_price" size="10">
                        <label>网站价</label>
                        <input type="text" value="<?php echo $team_rs['team_price'] ?>" class="number" id="team-create-team-price" name="team_price" size="10">
                        <label>虚拟购买</label>
                        <input type="text" value="<?php echo $team_rs['deposit'] ?>" class="number " id="team-create-deposit" name="deposit" size="10" lastvalue="0">
                    </td>
                </tr>
                <tr>
                    <td width="30%">数量: </td>
                    <td width="70%">
                        <label>最低数量</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="<?php echo $team_rs['min_number'] ?>" class="number" id="team-create-min-number" name="min_number" size="10">
                        <label>最高数量</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="<?php echo $team_rs['max_number'] ?>" class="number" id="team-create-max-number" name="max_number" size="10">
                        <label>每人限购</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="<?php echo $team_rs['per_number'] ?>" class="number" id="team-create-per-number" name="per_number" size="10">
                            <label>最低购买</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="<?php echo $team_rs['permin_number'] ?>" class="number" id="team-create-per-min-number" name="permin_number" size="10">
                        <span class="hint">最低数量必须大于0，最高数量/每人限购：0 表示没最高上限 （产品数|人数 由成团条件决定）</span> 
                    </td>
                </tr>
                <tr>
                    <td width="30%">库存: </td>
                    <td width="70%">
                        <label>产品库存量</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="<?php echo $team_rs['p_store'] ?>" class="number" id="team-create-min-number" name="p_store" size="10">
                        <label>产品库存预警数量量</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="<?php echo $team_rs['p_warnning'] ?>" class="number" id="team-create-min-number" name="p_warnning" size="10">
                    </td>
                </tr>
                <tr>
                    <td width="30%">时间: </td>
                    <td width="70%">
                        <label>开始时间</label>
                        <input type="text" maxlength="10" value="<?php echo date("Y-m-d",$team_rs['begin_time']) ?>" xt="" xd="" class="date" id="team-create-begin-time" name="begin_time" size="10">
                        <label>结束时间</label>
                        <input type="text" maxlength="10" value="<?php echo date("Y-m-d",$team_rs['end_time']) ?>" xt="13:54:30" xd="2015-02-28" class="date" id="team-create-end-time" name="end_time" size="10">
                        <label>优惠券有效期</label>
                        <input type="text" maxlength="10" value="<?php echo date("Y-m-d",$team_rs['expire_time']) ?>" class="number" id="team-create-expire-time" name="expire_time" size="10">
                        <span class="hint">时间格式：hh:ii:ss (例：14:05:58)，日期格式：YYYY-MM-DD （例：2010-06-10）</span>
                    </td>
                </tr>
                <tr>
                    <td width="30%">允许退款: </td>
                    <td width="70%">
                        <input type="checkbox" value="Y" <?php if($team_rs['allowrefund'] == Y){ ?> checked="checked" <?php } ?> name="allowrefund" class="allowrefund">&nbsp;是&nbsp;&nbsp;
                        <span style="font-size:12px;color:#989898;">本项目允许用户发起 申请退款</span>
                    </td>
                </tr>
                <tr>
                    <td width="30%">允许过期退款: </td>
                    <td width="70%">
                        <input type="checkbox" value="Y" <?php if($team_rs['outdatefun'] == Y){ ?> checked="checked" <?php } ?> name="outdatefun" class="allowrefund">&nbsp;是&nbsp;&nbsp;
                        <span style="font-size:12px;color:#989898;">本项目允许用户发起 过期申请退款</span>
                    </td>
                </tr>
                <tr>
                    <td width="30%">本单简介: </td>
                    <td width="70%">
                        <textarea  id="team-create-summary" name="summary" rows="5" cols="45" ><?php echo $team_rs['summary'] ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td width="30%">特别提示: </td>
                    <td width="70%">
                        <textarea  id="team-create-notice" name="notice" rows="5" cols="45" ><?php echo $team_rs['notice'] ?></textarea>
                    </td>
                </tr>

                <tr>
                    <td width="30%">商品名称: </td>
                    <td width="70%">
                        <input type="text"  value="<?php echo $team_rs['product'] ?>" id="team-create-product" name="product" size="30" >
                    </td>
                </tr>
                <tr>
                    <td width="30%">商品图片: </td>
                    <td width="70%">
                        <label>图片1</label>
                        <img src="<?php echo $team_rs['image1'] ?>"  alt="图片1" />
                        <input type="file" class="f-input" id="team-create-image" name="upload_image" size="30">
                        <br>
                        <label>图片2</label>
                        <img src="<?php echo $team_rs['image2'] ?>"  alt="图片2" />
                        <input type="file" class="f-input" id="team-create-image1" name="upload_image1" size="30">
                        <br>
                        <label>图片3</label>
                        <img src="<?php echo $team_rs['image3'] ?>"  alt="图片3" />
                        <input type="file" class="f-input" id="team-create-image1" name="upload_image2" size="30">
                    </td>
                </tr>
                <tr>
                    <td width="30%">本单详情: </td>
                    <td width="70%">
                         <textarea  class="" id="team-create-detail" name="detail" rows="5" cols="45" value=""><?php echo $team_rs['detail'] ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td width="30%"> </td>
                    <td width="70%">
                          <input type="submit" value="保存" >
                    </td>
                </tr>
            </table>
        </div>
        </form>
    </div>
    <div class="clear" style="clear:both;"></div>
    </div>
</div>
	