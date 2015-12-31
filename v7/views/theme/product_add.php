<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg; 
} ?> 
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css?v=112">
<script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
<script language="javascript">
    KindEditor.ready(function (K) {
        window.editor = K.create('#team-create-detail', {
                        items : [
        'source', '|', 'undo', 'redo', '|', 'preview', 
        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull',
         'clearhtml', 'quickformat', 'selectall', '/', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 
        'table', 'hr', 'baidumap', 
        'link', 'unlink','image'
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
        'link', 'unlink','image'
        ]
     }); 
 
    });
    $(function(){
        $('#itemclick').click(function(){
            $('#itemdiv').toggle();
        });
    });
</script>
<style type="text/css">
#itemdiv {display: none;}
</style>
<div class="page_content932">
    <div class="institutions_info">
 	<?php $this->load->view('theme/include/dashboard'); ?>
    <div class="Personal_center_right">
	    <div class="question_nav">
	        <ul>
	            <li ><a href="<?php echo site_url('counselor/product') ?>" >在线预约产品列表</a></li>
	            <li class="on"><a href="<?php echo site_url('counselor/product_add') ?>" >添加在线预约产品</a></li>
	        </ul>
	    </div>
        <form action="product_add?act=add" method="post" id="addform" enctype="multipart/form-data">
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
                        <option value="<?php echo $group_v['id']  ?>"><?php echo $group_v['name'] ?></option> 
                        <?php
                             } 
                        ?>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td width="30%">项目城市: </td>
                    <td width="70%">
                        <input type="checkbox"   value="0" name="city_ids[]" id="city_all">全部
                        <?php
                            foreach ($city_rs as $city_v) {
                        ?>
                        <input type="checkbox"   value="0" name="city_ids[]" ><?php echo $city_v['name'] ?>
                        <?php
                             } 
                        ?>
                    </td>
                </tr>
                <tr>
                    <td width="30%">地区类型: </td>
                    <td width="70%">
                        <select name="areatype">
                            <option value="0">特定区域</option> 
                            <option value="1">全部城市</option> 
                    </select>
                    </td>
                </tr>
               <!--  <tr>
                    <td width="30%">项目标签: </td>
                    <td>
                    <input type='button' id='itemclick' value='点击添加' />
                    <div id='itemdiv'>
                        <?php
                            foreach ($item_rs as $item_v) {
                        ?>
                        <input type="checkbox"   value="0" name="item_id[]" ><?php echo $item_v['name'] ?>
                        <?php
                             } 
                        ?>
                    </div>
                    </td>
                </tr> -->
                <tr>
                    <td width="30%">tag标签: </td>
                    <td width="70%">
                         <input type="text" value="None"  id="team-create-tags" name="tags" size="30">
                    </td>
                </tr>
                <tr>
                    <td width="30%">限制条件: </td>
                    <td width="70%">
                        <select style="width:160px;"  name="conduser">
                            <option value="Y">以购买成功人数成团</option><option selected="" value="N">以产品购买数量成团</option>
                        </select>
                        <select style="width:160px;"  name="buyonce">
                            <option selected="" value="Y">仅购买一次</option>
                            <option value="N">可购买多次</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="30%">项目标题: </td>
                    <td width="70%">
                         <input type="text"   value=""  id="team-create-title" name="title" size="30" lastvalue="">
                    </td>
                </tr>
                <tr>
                    <td width="30%">价格: </td>
                    <td width="70%">
                        <label>市场价</label>
                        <input type="text" value="1" class="number" id="team-create-market-price" name="market_price" size="10">
                        <label>网站价</label>
                        <input type="text" value="1" class="number" id="team-create-team-price" name="team_price" size="10">
                    </td>
                </tr>
                <tr>
                    <td width="30%">数量: </td>
                    <td width="70%">
                        <label>最低数量</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="10" class="number" id="team-create-min-number" name="min_number" size="10">
                        <label>最高数量</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="0" class="number" id="team-create-max-number" name="max_number" size="10">
                        <label>每人限购</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="1" class="number" id="team-create-per-number" name="per_number" size="10">
                            <label>最低购买</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="1" class="number" id="team-create-per-min-number" name="permin_number" size="10">
                        <span class="hint">最低数量必须大于0，最高数量/每人限购：0 表示没最高上限 （产品数|人数 由成团条件决定）</span> 
                    </td>
                </tr>
                <tr>
                    <td width="30%">库存: </td>
                    <td width="70%">
                        <label>产品库存量</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="0" class="number" id="team-create-min-number" name="p_store" size="10">
                        <label>产品库存预警数量量</label>
                        <input type="text" require="true" datatype="number" maxlength="6" value="0" class="number" id="team-create-min-number" name="p_warnning" size="10">
                    </td>
                </tr>
                <tr>
                    <td width="30%">时间: </td>
                    <td width="70%">
                        <label>开始时间</label>
                        <input type="text" maxlength="10" value="2015-02-27" xt="" xd="" class="date" id="team-create-begin-time" name="begin_time" size="10">
                        <label>结束时间</label>
                        <input type="text" maxlength="10" value="2015-02-28" xt="13:54:30" xd="2015-02-28" class="date" id="team-create-end-time" name="end_time" size="10">
                        <label>优惠券有效期</label>
                        <input type="text" maxlength="10" value="2015-05-27" class="number" id="team-create-expire-time" name="expire_time" size="10">
                        <span class="hint">时间格式：hh:ii:ss (例：14:05:58)，日期格式：YYYY-MM-DD （例：2010-06-10）</span>
                    </td>
                </tr>
                <tr>
                    <td width="30%">允许退款: </td>
                    <td width="70%">
                        <input type="checkbox" value="Y" name="allowrefund" class="allowrefund">&nbsp;是&nbsp;&nbsp;
                        <span style="font-size:12px;color:#989898;">本项目允许用户发起 申请退款</span>
                    </td>
                </tr>
                <tr>
                    <td width="30%">允许过期退款: </td>
                    <td width="70%">
                        <input type="checkbox" value="Y" name="outdatefun" class="allowrefund">&nbsp;是&nbsp;&nbsp;
                        <span style="font-size:12px;color:#989898;">本项目允许用户发起 过期申请退款</span>
                    </td>
                </tr>
                <tr>
                    <td width="30%">本单简介: </td>
                    <td width="70%">
                        <textarea  id="team-create-summary" name="summary" rows="5" cols="45" ></textarea>
                    </td>
                </tr>
                <tr>
                    <td width="30%">特别提示: </td>
                    <td width="70%">
                        <textarea  id="team-create-notice" name="summary" rows="5" cols="45" ></textarea>
                    </td>
                </tr>

                <tr>
                    <td width="30%">商品名称: </td>
                    <td width="70%">
                        <input type="text"  value="" class="f-input errorInput " id="team-create-product" name="product" size="30" lastvalue="">
                    </td>
                </tr>
                <tr>
                    <td width="30%">商品图片: </td>
                    <td width="70%">
                        <label>图片1</label>
                        <input type="file" class="f-input" id="team-create-image" name="upload_image" size="30">
                        <br>
                        <label>图片2</label>
                        <input type="file" class="f-input" id="team-create-image1" name="upload_image1" size="30">
                        <br>
                        <label>图片3</label>
                        <input type="file" class="f-input" id="team-create-image1" name="upload_image2" size="30">
                    </td>
                </tr>
                <tr>
                    <td width="30%">本单详情: </td>
                    <td width="70%">
                         <textarea  class="" id="team-create-detail" name="summary" rows="5" cols="45" lastvalue=""></textarea>
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
	