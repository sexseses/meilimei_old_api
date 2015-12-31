<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg;
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" >
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>/editor/croppic.css" >
<script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/croppic.min.js"></script>
<style type="text/css">
    table {}
    table td {padding:3px; margin:5px 0px;}
    .swipe {position: relative; top: 0px; left:0px; display: inline-block;}
    .swipe img { vertical-align: middle;}
    .drag{border: 2px solid #f00; width: 10px; height: 10px; cursor: move; position: absolute; left: 0; top: 0;}
</style>
<script language="javascript" type="text/javascript">
    KindEditor.ready(function (K) {
        window.editor = K.create('#content', {
            items : [
                'source', '|', 'undo', 'redo', '|', 'preview', 'template', 'code', 'cut', 'copy', 'paste',
                'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
                'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
                'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
                'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|',
                'media', 'insertfile', 'table', 'hr',   'baidumap', 'pagebreak',
                'anchor', 'link', 'unlink'
            ]
        });
        window.editor = K.create('#description',{width:'700px'});
    });

</script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li><a href="<?php echo site_url('manage/diary'); ?>">美人记管理</a></li>
                    <li class="on"><a href="<?php echo site_url('manage/diary/add'); ?>">添加</a></li>
                    <li><a href="<?php echo site_url('manage/diary/category'); ?>">目录管理</a></li>
                    <li><a href="<?php echo site_url('manage/diary/addcategory'); ?>">添加目录</a></li>
                    <li><a href="<?php echo site_url('manage/diary/comments'); ?>">评论管理</a></li>
                    <li><a href="<?php echo site_url('manage/diary/check'); ?>">待审核</a></li>
                    <li><a href="<?php echo site_url('manage/diary/total'); ?>">统计</a></li>
                </ul>
            </div><style type="text/css">.manage_yuyue label{display:inline-block; padding:2px 20px 2px 0;font-size:12px;}</style>
            <div class="clear" style="clear:both;"></div>
            <div class="manage_yuyue">
                <div><?php echo form_open_multipart('manage/diary/add') ?>
                    <table>
                        <tr>
                            <td  style="width:80px;">标题: </td>
                            <td><textarea name="content" row="5" col="60"><?php echo $result[0]->content;?></textarea></td>
                        </tr>
                        <tr>
                            <td>发布的用户</td>
                            <td><input type="text" value="<?php echo $result[0]->username;?>" name="user_id" id="user_id" /><input type="hidden" id="uid" name="uid" value="<?php echo $result[0]->uid;?>" /></td>
                        </tr>
                        <tr>
                            <td>目录</td>
                            <td><select name="ncid" id="category"><option value="<?php echo $result[0]->ncid;?>"><?php echo $result[0]->title;?></option></option></select></td>
                        </tr>
                        <tr style="width:560px; ">
                            <td>图片</td>
                            <td >
                                <span style="display:block;" id="thisxy"></span>
                                <div class="swipe" style="width:480px; float:left;">
                                    <div id="cropContainerModal" style="border: 1px solid #ccc; height: 480px; position: relative; width: 480px;" ><img src="http://pic.meilimei.com.cn/upload/<?php echo $result[0]->imgurl;?>" class="croppedImg" style="height: 480px;width: 480px;"></div>
                                    <div class="drag" style="top: <?php echo $result[0]->pointY.'%';?>; left: <?php echo $result[0]->pointX.'%';?>">1</div>
                                </div>
                                <div style="float: left; margin-left:20px" >
                                    <input type="hidden" name="filepath" id="filepath" value="http://pic.meilimei.com.cn/upload/<?php echo $result[0]->imgurl;?>" style="width:280px; padding:3px 5px"/>
                                    <label>添加手术内容</label><br />
                                    <input type="text" name="tag" id="tag" value="<?php echo $result[0]->item_name;?>"  style="width:280px; padding:3px 5px"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>标签</td>
                            <td>
                                 <table >
                                    <tr>
                                        <th style="width:35%">名称</th>
                                        <th style="width:20%">操作</th>
                                    </tr>
                                    <?php foreach($items_rs as $row){ ?>
                                        <tr class="Item<?php echo $row['id']; ?>" title="Item<?php echo $row['id']; ?>">
                                                <td style="width:35%;text-align:center"><?php echo $row['name'] ?></td>
                                                <td style="width:20%;text-align:center"><input type="button"  value="显示子项目" onclick = "displayItem(<?php echo $row['id'] ?>)"></td>
                                        </tr>
                                        <?php foreach($row['child'] as $child){ ?>
                                            <tr style="display:none;" class="childItem<?php echo $row['id']; ?>" title="childItem<?php echo $row['id']; ?>">
                                                <td style="width:35%;text-align:center"><input type="radio" name="item_name" value= "<?php echo $child['name'] ?>" /> <?php echo $child['name'] ?></td>
                                                <td style="width:20%;text-align:center"><input type="button" id='displayitem' value="显示子项目" onclick = "displayThreeItem(<?php echo $child['id'] ?>)"></td>
                                            </tr>
                                            <?php foreach($child['three_child'] as $three_child){ ?>
                                                <tr style="display:none;" class="threechildItem<?php echo $child['id']; ?>" title="threechildItem<?php echo $row['id']; ?>">
                                                    <td style="width:35%;text-align:center"><input type="radio" name="item_name" value= "<?php echo $three_child['name'] ?>" /> <?php echo $three_child['name'] ?></td>
                                                     <td style="width:20%;text-align:center"></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="45">浏览次数</td>
                            <td><input type="text" value="<?php echo rand(30000,50000) ?>" name="views" id="views" />  </td>
                        </tr>
                        <tr>
                            <td  style="width:80px;">价格: </td>
                            <td><input type="text" name="item_price" id="item_price" value="<?php echo $result[0]->item_price;?>"/></td>
                        </tr>
                        <tr>
                            <td  style="width:80px;">医生: </td>
                            <td><input type="text" name="doctor" id="doctor" value="<?php echo $result[0]->doctor;?>"/></td>
                        </tr>
                        <tr>
                            <td  style="width:80px;">医院: </td>
                            <td><input type="text" name="hospital" id="hospital" value="<?php echo $result[0]->hospital;?>"/></td>
                        </tr>
                        <tr>
                            <td  style="width:80px;">排序: </td>
                            <td><input type="text" name="sort" id="sort" value="<?php echo $result[0]->sort;?>"/></td>
                        </tr>
                        <tr>
                            <td>推荐启动页面: </td>
                            <td><input type="radio" name="loading" value="1" id="isloading1" <?php if($result[0]->loading == 1){ echo "checked";}?>/><label for="isloading1">是</label><input type="radio" name="loading" id="isloading0" value="0" <?php if($result[0]->loading == 0){ echo "checked";}?>/><label for="isloading0">否</label></td>
                        </tr>
                        <tr>
                            <td>推荐首页: </td>
                            <td><input type="radio" name="isFront" value="1" id="isFront1" <?php if($result[0]->is_front == 1){ echo "checked";}?>/><label for="isFront1">是</label><input type="radio" name="isFront" id="isFront0" value="0" <?php if($result[0]->is_front == 0){ echo "checked";}?>/><label for="isFront0">否</label></td>
                        </tr>
                        <tr>
                            <td>发布日期</td>
                            <td><input type="text" value="<?php echo date('Y-m-d',$result[0]->updated_at);?>" name="ctime" id="ctime" class="datepicker" /> 小时分钟<input type="text" name="hour" value="<?php echo date('H:i',$result[0]->updated_at);?>" size="10"> </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" name="type" value="1"/>

                                <input type="hidden" name="nid" value="<?php echo $result[0]->nid;?>"/>
                                <input type="hidden" name="pointY" id="pointY" value="<?php echo $result[0]->pointY;?>"/>
                                <input type="hidden" name="pointX" id="pointX" value="<?php echo $result[0]->pointX;?>"/>
                            </td>
                            <td><input type="submit" value="提交" style="padding:2px 5px;margin:10px 0;" onclick="$('#filepath').val($('.croppedImg').attr('src'));"></td>
                        </tr>
                    </table>
                    </form>

                </div>
                <script language="javascript">
                    function displayItem(childid){
                        $("tr[title^='Item']").toggle();
                        $(".childItem" + childid ).toggle();
                    }

                    function displayThreeItem(childid){
                        $(".childItem"+childid).toggle();
                        $(".threechildItem"+childid).toggle();
                    }


                    $("#user_id").autocomplete({
                        source: "/manage/topic/Suser",
                        minLength: 2,
                        select: function(event, ui) {
                            $('#uid').val(ui.item.id);
                            $.getJSON("../topic/dc?uid="+ ui.item.id,function(result){ $('#category').empty();$.each(result,function(k,v){
                                $('#category').append('<option value="' + v.ncid + '">' + v.title + '</option>')
                            });})
                        }
                    });

                    $("#doctor").autocomplete({
                        source: "/manage/topic/Sdoctor",
                        minLength: 2,
                        select: function(event, ui) {
                            $('#doctor').val(ui.item.alias);
                        }
                    });


                    $("#hospital").autocomplete({
                        source: "/manage/topic/Shospital",
                        minLength: 2,
                        select: function(event, ui) {
                            $('#hospital').val(ui.item.name);
                        }
                    });
                    $(".datepicker").datepicker({ dateFormat: "yy-mm-dd" }).val();
                    var croppicContainerModalOptions = {
                        uploadUrl:'/manage/diary/img_save_to_file',
                        cropUrl:'/manage/diary/img_crop_to_file',
                        modal:true,
                        imgEyecandyOpacity:0.4,
                        loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> '
                    }
                    var cropContainerModal = new Croppic('cropContainerModal', croppicContainerModalOptions);

                    $(function(){
                        var _move = false,_x, _y,x,y,_wh = 480;
                        function min_max(c){if(c<0){return 0;}else if(c>_wh){return _wh;}else{return c;}}
                        $(".drag").mousedown(function(e){
                            _move = true;
                            _x = e.pageX - parseInt($(".drag").css("left"));
                            _y = e.pageY - parseInt($(".drag").css("top"));
                        });
                        $(document).mousemove(function(e) {
                            if (_move) {
                                x = min_max(e.pageX - _x);
                                y = min_max(e.pageY - _y);
                                $(".drag").css({ top: Math.round(y / _wh * 10000) / 100.00 + "%", left: Math.round(x / _wh * 10000) / 100.00 + "%" });
                                $("#pointY").val(Math.round(y / _wh * 10000) / 100.00);
                                $("#pointX").val(Math.round(x / _wh * 10000) / 100.00);
                                //$('#thisxy').html('Y:'+Math.round(y / _wh * 10000) / 100.00 + "%"+'-------X:'+Math.round(x / _wh * 10000) / 100.00 + "%")
                            }
                        }).mouseup(function() { _move = false; });
                    });

                </script>

                <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/rl_exp.js"></script>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>

    </div>
</div>