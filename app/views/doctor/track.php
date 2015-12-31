<?php if(!isset($terror) || $terror !=1):?>
<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
	$(".dataalert").each(function(){
		var stringObj = $(this).html();
		var newstr=stringObj.replace(/{{/gm,'<img src="http://pic.meilimei.com.cn/upload/').replace(/}}/gm,'" />');
		$(this).html(newstr);
	});
});
</script>
<style>
ul,li {list-style:none; padding:0; margin:0;}
img {width:150px;}
.manage_yuyue_form {}
.manage_yuyue_form .title li {float: left; padding: 5px;}
.manage_yuyue_form .dd { border-bottom: 1px dashed #ccc; overflow: hidden; padding: 10px;}
.manage_yuyue_form .dd li  { float: left;}
table {border:#eee 1px solid; font-size:14px;}
thead {background:#eee;font-size: 15px;}
tbody {}
tbody tr {}
th {padding: 10px 0;}
td {padding:20px 10px;border-bottom: 1px solid #eee;}
.dataalert {}
.dataalert li {margin: 5px 0;}
.paging {background:#eee;padding: 10px 0; text-align: center;}
.paging a {color:#000; }
</style>
<div class="manage_yuyue" > 
	<div class="manage_yuyue_form">
		<table>
		 <thead>
			<tr>
			  <th>ID</th>
			  <th>主题</th>
			  <th>内容</th>
			  <th>时间</th>
			</tr>
		  </thead>
		  <tbody>
		<?php $i=1;  foreach($results as $row): ?>
			<tr>
			<td><?php echo $row->id ; ?></td>
			<td><?php echo $row->title; ?></td>
			<td class="dataalert"><?php echo $row->data; ?></td>
			<td><?php echo date('Y-m-d',$row->cdate); ?></li> 
			</tr>
		<?php $i++; endforeach; ?>
		</table>

	</div>
	<div class="paging">
		<?php echo $pagelink ?>
	</div>
</div>
<script type="text/javascript"> 
	$(function () {
    $(".forbiduser").click(function () {
        var curobj = $(this);
        curobj.text('处理中');
        $.get('http://www.meilimei.com/manage/topic/userbanned/', {
            "uid": curobj.attr('data-id'),
            "banned": 1
        }, function (data) {
            curobj.text('已禁用');
        })
	});
        $("#selectall").click(function () {
            $(".manage_yuyue_form :checkbox").attr("checked", true);
        });
        $("#deltall").click(function () {
            
            $(".manage_yuyue_form :checkbox:checked").each(function () {
                $(this).parent().parent().hide(300);
                $.get("http://www.meilimei.com/manage/topic/cdel/" + $(this).val(), {
                    id: $(this).val()
                }, function (data, textStatus) {}, "json");
            })
        })
    });
 </script>
 <?php else:?>
 <center>查询错误</center>
 <?php endif; ?>