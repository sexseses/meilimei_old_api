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
.dataalert li {border-bottom: 1px dashed #ccc; vertical-align: top; width: 100%;}
.dataalert img {width:100px; margin-right:10px;}
</style>
<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo base_url('manage/apple')?>">广告管理</a></li>
                    <li><a href="<?php echo base_url('manage/apple/add')?>">添加广告</a></li>
                    <li class="on"><a href="<?php echo base_url('manage/apple/track')?>">统计</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
						<div class="clear" style="text-align: right;  padding: 5px; text-decoration: none; font-size:15px;"><a href="<?php echo 'http://www.meilimei.com/manage/apple/trackexcel/'.$param; ?>">导出EXCEL</a></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:10%"> ID</li>  
                                    <li style="width:25%">主题</li>
                                    <li style="width:50%">内容</li> 
                                    <li style="width:10%">时间</li> 
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php $i=1;  foreach($results as $row): ?>
									<ul>
                                	<li style="width:10%"><?php echo $row->id ; ?></li>  
                                    <li style="width:25%"><?php echo $row->title; ?></li>
                                    <li class="dataalert" style="width:50%"><?php echo $row->data; ?></li> 
                                    <li style="width:10%"><?php echo date('Y-m-d',$row->cdate); ?></li> 
									</ul>
									 <div class="clear" style="clear:both;">  </div>
								<?php $i++; endforeach; ?>
                         
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <?php echo $pagelink ?>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> <script type="text/javascript"> 
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
  </div>
</div>
