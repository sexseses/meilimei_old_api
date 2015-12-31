<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg;
} ?>
<style type="text/css">
    .comments
    {    background: rgba(0, 0, 0, 0.5);
    height: 100%;
    margin-bottom: 50px;
    position: fixed;
    top: 0;
    width: 100%; display: none;
    }
    .comments form { background:  #fff;
        margin: 10% auto 0;
        padding: 20px;
        width: 800px;position: relative;}
    #ccccolse {  position: absolute;
        right: 10px;
        top: 10px;}
</style>
<div class="page_content937">
    <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li><a href="<?php echo site_url('manage/diary'); ?>">美人记管理</a></li>
                    <li><a href="<?php echo site_url('manage/diary/add'); ?>">添加</a></li>
                    <li><a href="<?php echo site_url('manage/diary/category'); ?>">目录管理</a></li>
                    <li><a href="<?php echo site_url('manage/diary/addcategory'); ?>">添加目录</a></li>
                    <li class="on"><a href="<?php echo site_url('manage/diary/comments'); ?>">评论管理</a></li>
                    <li><a href="<?php echo site_url('manage/diary/check'); ?>">待审核</a></li>
                    <li><a href="<?php echo site_url('manage/diary/total'); ?>">统计</a></li>
                </ul>
            </div>
            <div class="manage_yuyue" >
                <div class="manage_search"><form method="get" action="<?php echo base_url('manage/diary/comments'); ?>">
                        <ul>
                            <li>类型<select name="types"><option value="">全部</option><option <?php echo $this->input->get('types')=='1'?'selected="selected""':'' ?> value="1">windows</option><option <?php echo $this->input->get('types')=='0'?'selected="selected""':'' ?> value="0">非 windows</option></select></li>
                            <li>关键字<input type="text" value="<?php echo $this->input->get('kw') ?>" name="kw" /></li>
                            <li>用户id<input type="text" value="<?php echo $this->input->get('uid') ?>" name="uid" /></li>
                            <li><input  name="submit" type="submit" value="搜索" class="search"></li>
                        </ul></form>
                </div>
                <div class="manage_yuyue_form">
                    <ul> <li style="width:8%">编号</li>
                        <li style="width:18%">主题用户</li>
                        <li style="width:18%">名称</li>
                        <li style="width:7%">评论用户</li>
                        <li style="width:8%">内容</li>
                        <li style="width:10%">操作者</li>
                        <li style="width:10%">时间</li>
                        <li class="width:25%">操作</li>
                        <div class="clear" style="clear:both;"></div>
                    </ul>
                    <?php

                    foreach($results as $row){
                        $this->db->where('id',$row->fromuid);
                        $t = $this->db->get('users')->result_array();
                        echo ' <ul class="">
                                	 <li style="width:8%">'.$row->cid .'</li>
                                     <li style="width:18%">'.$row->username.'<br/><a onclick="sendcomment('.$row->nid.')">回复</a></li>
                                    <li style="width:18%">'.$row->ncontent.'</br><img width="150px" height="200px" src="'.$row->nimgurl .'"/></li>
                                    <li style="width:7%">'.$t[0]['phone'].'<br/><a onclick="sendcomment('.$row->nid.','.$row->cid.')">回复</a></li>
                                    <li style="width:8%">'.$row->content .'</li>
                                    
                                    <li style="width:10%">'.$row->oper.'</li>
                                    <li style="width:10%">'.date('Y-m-d H:i:s',$row->created_at).'</li>
								    <li class="width:15%">'.($row->banned==1?'已禁用':' <a  class="forbiduser" data-id="'.$row->fromuid .'" title="禁用改用户">禁用</a>').'&nbsp;&nbsp;<a onclick="return confirm(\'确认删除 '.$row->cid.'?\')" href="'.site_url('manage/diary/delcomment/'.$row->cid).'">删除</a></li> <div class="clear" style="clear:both;"></div>
                                </ul>';
                    }
                    ?>

                    <div class="clear" style="clear:both;"></div>
                </div>
                <div class="paging">
                    <div class="paging_right" >
                        <?php echo $pagelink ?>
                    </div>
                </div>
            </div>
        </div><script type="text/javascript">
            $(function(){
                $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
                $(".Vertical24").click(function(){
                    var _obj = $(this);
                    if(_obj.text()=='正常'){  _obj.text('处理中');
                        $.get('<?php echo site_url('manage/yishi/userac'); ?>', {"uid":$(this).attr('data-id'),"banned":1}, function(data) {
                            _obj.text('禁用');
                        })
                    }else if(_obj.text()=='禁用'){ _obj.text('处理中');
                        $.get('<?php echo site_url('manage/yishi/userac'); ?>', {"uid":$(this).attr('data-id'),"banned":0}, function(data) {
                            _obj.html('<a>正常</a>');

                        })
                    }
                });

            })
        </script>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>


<div class="comments" style="margin-bottom:50px">
    <form enctype="multipart/form-data" id="sendx" accept-charset="utf-8" method="post" action="">
        <span id="ccccolse">关闭</span>
        <ul style="padding:10px;">
            <li><label style="width:100px; display:inline-block">回复评论的ID</label><input style="padding:2px;" value="0" type="text" name="commentTo" id="commentTo" /> </li>
            <li><label style="width:100px; display:inline-block">用户名</label><input style="padding:2px;" value="" type="text" name="user_id" id="user_id" /><input type="hidden" id="suser_id" name="fuid" value="<?php echo $preview ?>" /></li>
            <li><label style="width:100px; display:inline-block">图片</label> <input type="file" name="attachPic" /></li><li><label style="width:100px; display:inline-block">是否推送用户通知</label> <input type="checkbox" name="push" value="1"/></li>
            <li style="margin-top:10px; height:auto;position:relative; "><label style="width:100px; display:inline-block">内容</label> <textarea id="rl_exp_input" style="padding:1px;width:500px;height:50px;" name="comment"></textarea> <div style="position:relative;width:100%;padding-left:120px;float:left">
                    <a href="javascript:void(0);" id="rl_exp_btn">表情</a>
                    <div class="rl_exp" id="rl_bq" style="display:none;">
                        <ul class="rl_exp_tab clearfix">
                            <li><a href="javascript:void(0);" class="selected">默认</a></li>
                            <li><a href="javascript:void(0);">拜年</a></li>
                        </ul>
                        <ul class="rl_exp_main clearfix rl_selected"></ul>
                        <ul class="rl_exp_main clearfix" style="display:none;"></ul>
                        <a href="javascript:void(0);" class="close">×</a>
                        <li style="padding:10px 10px 10px 100px;"><input style="display:none" id="sendcomment" type="submit" name="submit" value="发布" style="padding:2px 10px;" /></li>
        </ul>
    </form>
</div>
</div>

<script>

    function sendcomment(nid, cid){
        if(typeof(cid) == 'undefined') {
            document.getElementById('sendx').action='http://www.meilimei.com/manage/diary/sendcommentx/' + nid + '.html';
            $('#commentTo').val(0);
        }else{
            document.getElementById('sendx').action='http://www.meilimei.com/manage/diary/sendcommentx/' + nid + '.html';
            $('#commentTo').val(cid);
        }
        $('.comments').show();
    }
    $('#ccccolse').bind('click',function(){
        $('.comments').hide();
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
    $(function() {
        $("#commenttype").val('topic');
        var types = 'topic';
        $("#user_id").autocomplete({
            source: "../../topic/Suser?type="+types,
            minLength: 2,
            select: function(event, ui) {

                $('#suser_id').val(ui.item.id);
                $("#sendcomment").show();
            }
        });
    });
</script>