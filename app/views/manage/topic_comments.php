<style>
    .manage_yuyue_form #commentslist li{margin: 10px 0;list-style: none; height: auto;} 
	.hui{ background:#fff; box-shadow: 0 0 5px #000; display: none; font-size: 12px; left: 0; margin: 0 !important; overflow: hidden; padding: 10px; position: absolute; text-align: center; top: 65px; width: 100%; z-index: 1;}
    .hui textarea{width: 98%;height: 60%; resize:none;}
    .info{width: 100px; height: auto;overflow: hidden;}
	#commentslist {position: relative;}
	.commentslists {height: 280px; position: relative;}
	.zceng {position: absolute; top: 0%; left: 0%; width: 100%; height: 100%; background-color: black; -moz-opacity: 0.7; opacity:.70; filter: alpha(opacity=70);}
</style>
<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg; 
} ?> 
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" > 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                        <div class="question_nav">
                            <ul>
                                <li><a href="<?php echo site_url('manage/topic'); ?>">话题管理</a></li>
                                <li><a href="<?php echo site_url('manage/topic/add'); ?>">添加话题</a></li>
                                <li><a href="<?php echo site_url('manage/topic/nocla'); ?>">未分类</a></li>
                                <li><a href="<?php echo site_url('manage/topic/order'); ?>">推荐排序</a></li>
                                <li><a href="<?php echo site_url('manage/topic/setting'); ?>">话题配置</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/topic/comments')."?types=1"; ?>">评论</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                            <div class="manage_search"><form method="get" action="<?php echo base_url('manage/topic/comments'); ?>">
                                <ul> 
                                    <li>来源<select name="types"><option value="1" <?php echo $this->input->get('types')=='1'?'selected="selected""':'' ?>>非windows</option><option value="" <?php echo $this->input->get('types')==''?'selected="selected""':'' ?>>全部</option><option <?php echo $this->input->get('types')=='IOS'?'selected="selected""':'' ?> value="IOS">IOS</option><option value="windows" <?php echo $this->input->get('types')=='windows'?'selected="selected""':'' ?>>windows</option><option value="Android" <?php echo $this->input->get('types')=='Android'?'selected="selected""':'' ?>>Android</option></select></li> 
                                    <li>关键字<input type="text" value="<?php echo $this->input->get('kw') ?>" name="kw" /></li> 
                                    <li>用户id<input type="text" value="<?php echo $this->input->get('uid') ?>" name="uid" /></li> 
                                    <li><input  type="submit" value="搜索" class="search"></li>
                                </ul></form> 
                            </div>
                            <div class="manage_yuyue_form">
                                <ul>
                                    <li style="width:10%"><a id="selectall">[全部]</a> ID</li>  <li style="width:10%">主题用户</li>
                                    <li style="width:15%">主题</li><li style="width:10%">评论用户</li><li style="width:15%">内容</li><li style="width:7%">操作者</li><li style="width:8%">来源</li>
                                    <li style="width:10%">时间</li>
                                    <li style="width:10%"><a id="deltall">[删除]</a> 操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php  
                                foreach($results as $row){ $tmp = unserialize($row->type_data);
                                    $s = $row->picture?'<img src="'.$row->picture.'" width=180 height=180/>':'';
                                    
                                    echo '<ul style="opacity:'.($row->banned==1?0.3:1).'" id="commentslist">
                                    <li style="width:10%"><input type="checkbox" value="'.$row->id.
                                    '" name="seclc[]" /> '.$row->id.'</li><li style="width:10%"><a href="'.site_url('manage/users/detail/'.$row->uid).'">'.$row->mPhone.'</a><br/><div class="info"><a href="javascript:void(0);" class="hf">回复</a></div><div class="hui" style="margin-bottom:50px">'.form_open_multipart('manage/topic/comment/'.$row->contentid).'
                            <table>
                            <tr><td align="right"><label style="width:100px; display:inline-block">回复评论的ID</label></td><td align="left"><input style="padding:2px;" value="0" type="text" name="commentTo" id="commentTo" /></td></tr>                         
                            <tr><td align="right"><label style="width:100px; display:inline-block">类型</label></td><td align="left">
                            <select id="commenttype" name="commenttype">
                            <option selected="selected" value="topic">话题</option>
                            <option value="ans">咨询</option>
                            </select>
                            </td>
                            </tr>
                            <tr>
                            <td align="right"><label style="width:100px; display:inline-block">用户名</label></td><td align="left"><input style="padding:2px;" value="" type="text" name="user_id" id="user_id_'.$row->id.'" onkeyup="autocomplete1('.$row->id.')"/><input type="hidden" id="suser_id_'.$row->id.'" name="fuid" value="" /></td></tr>
                            <tr align="right"><td><label style="width:100px; display:inline-block">图片</label></td><td align="left"><input type="file" name="attachPic" /></td></tr>
                            <tr align="right"><td><label style="width:100px; display:inline-block">是否推送用户通知</label></td><td align="left"><input type="checkbox" name="push" value="1"/></td></tr>
                            <tr><td align="right"><label style="width:100px; display:inline-block">内容</label></td><td align="left"><textarea id="rl_exp_input" style="padding:1px;width:500px;height:50px;" name="comment"></textarea></td></tr>
                            <!--tr><td colspan="2">
                                <a href="javascript:void(0);" id="rl_exp_btn">表情</a>
                                <div class="rl_exp" id="rl_bq" style="display:none;">
        <ul class="rl_exp_tab clearfix">
            <li><a href="javascript:void(0);" class="selected">默认</a></li>
            <li><a href="javascript:void(0);">拜年</a></li> 
        </ul>
        <ul class="rl_exp_main clearfix rl_selected"></ul>
        <ul class="rl_exp_main clearfix" style="display:none;"></ul> 
        <a href="javascript:void(0);" class="close">×</a></div>
        </td></tr-->
        <tr><td align="right">
        <input type="hidden" name="touid" value="'.$row->uid.'" /><input type="hidden" name="to-uid" value="'.$row->uid.'" /></td><td align="left"><input style="display:none"  onclick="this.style.display=\'none\'" id="sendcomment_'.$row->id.'" type="submit" name="submit" value="发布" style="padding:2px 10px;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="canel" value="取消" onclick="canel1('.$row->id.');"/></td></tr>
                            </table>
                            </form>
                            </div></li>
                                    <li style="width:15%"><a href="'.site_url('manage/topic/detail/'.$row->contentid).'">'.$tmp['title'].'</a></li><li style="width:10%"><a href="'.site_url('manage/users/detail/'.$row->fuid).'">'.$row->phone.'</a><br/><div class="info"><a href="javascript:void(0);" class="hf">回复</a></div><div class="hui" style="margin-bottom:50px">'.form_open_multipart('manage/topic/comment/'.$row->contentid).'
                            <table>
                            <tr><td align="right"><label style="width:100px; display:inline-block">回复评论的ID</label></td><td align="left"><input style="padding:2px;" value="'.$row->id.'" type="text" name="commentTo" id="commentTo" /></td></tr>                         
                            <tr><td align="right"><label style="width:100px; display:inline-block">类型</label></td><td align="left">
                            <select id="commenttype" name="commenttype">
                            <option selected="selected" value="topic">话题</option>
                            <option value="ans">咨询</option>
                            </select>
                            </td>
                            </tr>
                            <tr>
                            <td align="right"><label style="width:100px; display:inline-block">用户名</label></td><td align="left"><input style="padding:2px;" value="" type="text" name="user_id" id="cuser_id_'.$row->id.'" onkeyup="autocomplete2('.$row->id.')"/><input type="hidden" id="csuser_id_'.$row->id.'" name="fuid" value="" /></td></tr>
                            <tr align="right"><td><label style="width:100px; display:inline-block">图片</label></td><td align="left"><input type="file" name="attachPic" /></td></tr>
                            <tr align="right"><td><label style="width:100px; display:inline-block">是否推送用户通知</label></td><td align="left"><input type="checkbox" name="push" value="1"/></td></tr>
                            <tr><td align="right"><label style="width:100px; display:inline-block">内容</label></td><td align="left"><textarea id="rl_exp_input" style="padding:1px;width:500px;height:50px;" name="comment"></textarea></td></tr>
                            <!--tr><td colspan="2">
                                <a href="javascript:void(0);" id="rl_exp_btn">表情</a>
                                <div class="rl_exp" id="rl_bq" style="display:none;">
        <ul class="rl_exp_tab clearfix">
            <li><a href="javascript:void(0);" class="selected">默认</a></li>
            <li><a href="javascript:void(0);">拜年</a></li> 
        </ul>
        <ul class="rl_exp_main clearfix rl_selected"></ul>
        <ul class="rl_exp_main clearfix" style="display:none;"></ul> 
        <a href="javascript:void(0);" class="close">×</a></div>
        </td></tr-->
        <tr><td align="right">
        <input type="hidden" name="touid" value="'.$row->uid.'" /><input type="hidden" name="to-uid" value="'.$row->uid.'" /></td><td align="left"><input style="display:none" id="csendcomment_'.$row->id.'" type="submit" name="submit" value="发布" style="padding:2px 10px;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="canel" value="取消" onclick="canel2('.$row->id.')"/></td></tr>
                            </table>
                            </form>
                            </div></li><li style="width:15%">'.$row->comment.'<br/>'.$s.'</li> <li style="width:7%">'.($row->loginuser!='nouser'?'<a href="'.site_url('manage/users/detail/'.$row->fuid).'">'.$row->loginuser.'</a>':'').'</li><li style="width:8%">'.$row->device.'</li>
                                    <li style="width:10%">'.date('Y-m-d H:i',$row->cTime).'</li>
                                   <li style="width:10%"><a onclick="return confirm(\'确认删除 '.$tmp['title'].'?\')" href="'.base_url('manage/topic/cdel/'.$row->id).'" >删除</a>'.($row->banned==1?'已禁用':' <a  class="forbiduser" data-id="'.$row->fuid .'" title="禁用改用户">禁用</a>').'</li> </ul> <div class="clear" style="clear:both;">  </div>';
                                }
                                ?>
                         
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" style="<?php echo $issubmit?'display:none':'' ?>">
                                    <ul>
                                        <li><a href="<?php echo $preview ?>" class="preview">&nbsp;</a></li>
                                        <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                    </ul>
                                    <h5>第<?php echo $offset ?>-<?php echo $offset+count($results)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                                </div>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> <script type="text/javascript"> 

    function sendamount(amount,dataid){
        $.get('<?php echo site_url() ?>jquery/tuijianset', {"dataid":dataid,"weight":amount}, function(data) {  });
    }

    function autocomplete1(id){
        //alert(id);
        var types = 'topic';
            $("#user_id_"+id).autocomplete({
                    source: "../../topic/Suser?type="+types,
                    minLength: 2,
                    select: function(event, ui) { 
                        
                        $('#suser_id_'+id).val(ui.item.id);
                        $("#sendcomment_"+id).show();
                    }
                });
    }
    
    function canel1(id){
        
        $(".hui").hide();
    }

    function canel2(id){
		$("#commentslist").each(function(){
		  $(this).removeClass();
		});
        $(".hui").hide();
    }

    function autocomplete2(id){
        //alert(id);
        var types = 'topic';
            $("#cuser_id_"+id).autocomplete({
                    source: "../../topic/Suser?type="+types,
                    minLength: 2,
                    select: function(event, ui) { 
                        
                        $('#csuser_id_'+id).val(ui.item.id);
                        $("#csendcomment_"+id).show();
                    }
                });
    }

    $(function () {

    $(".hf").click(function(){
        $(this).parent(".info").siblings(".hui").show();
        $(this).parents("#commentslist").addClass("commentslists");
    })

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
<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/rl_exp.js"></script>  
  </div>
</div>
