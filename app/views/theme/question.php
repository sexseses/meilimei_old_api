<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css">
<div class="page_content932">
    <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js?v=002"></script>
    <div class="institutions_info">
        <?php $this->load->view('theme/include/dashboard'); ?>
        <div class="Personal_center_right">
            <div class="question_shortcuts">
                <ul>
                    <li><a href="<?php echo site_url('user/dashboard'); ?>">待回答问题</a>><a>解答问题</a></li>
                    <li class="on"><?php echo isset($backward[0]['id']) ? '<a href="' . site_url('question/' . $backward[0]['id']) . '">上一个问题</a>' : '';   echo isset($forward[0]['id']) ? '<a href="' . site_url('question/' . $forward[0]['id']) . '">下一个问题</a>' : ''; ?></li>
                </ul>
            </div>
            <div class="question_detail">
                <h4>【问题】<?php echo $questions[0]['title'] ?></h4>

                <p><?php echo $questions[0]['description']; if (!empty($attaches)) echo '<br><img src="http://pic.meilimei.com.cn/upload/' . $attaches[0]['savepath'] . '"/>';?></p>
                <h6><?php echo  date('Y-m-d', $questions[0]['cdate']) ?>, <?php echo $diff ?> </h6>
            </div>
            <?php
           
            $yishen = array();
            foreach ($answers as $row) {
                echo '<div class="question_answered"> <h5>' . ($current_uid == $row['uid'] ? '我的回答' : '用户:' . substr($row['username'], 0, 3) . '** ') . (($current_uid == $questions[0]['fUid'] || $current_uid == $row['uid']) ? '<em class="replay" talk-id="' . $row['uid'] . '" data-id="' . $row['qid'] . '">交谈</em>' : '') . '</h5>
                            <p>' . $row['content'] . '</p>
                            <h6> 回答时间：' . date('Y-m-d', $row['cdate']) . '</h6>
                        </div>';
                $yishen[$row['uid']] = true;
            }
            if ((count($yishen) < 5 && $questions[0]['fUid'] != $current_uid && $this->wen_auth->get_role_id() == 2 && !isset($yishen[$uid])) || $this->session->userdata('admin_answer')|| $this->session->userdata('yiyuan_answer')) { //管理员代替医师回答问题
                ?>

                <div class="question_answere_box">
                    <h4 id="acfrom"><a title="点击回答问题">我要回答该问题>></a></h4>

                    <div id="formcontroll"><?php echo form_open("question/answer", array('id' => 'aquestion')) ?>
                        <textarea name="myaswer" id="myaser" cols="" rows="" class="write_box"></textarea><input type="hidden"
                                                                                                     name="qid"
                                                                                                     value="<?php echo $questions[0]['id'] ?>"/><input
                            type="hidden" name="answerauth" value="<?php echo $answerauth ?>"/>

                        <div class="emotion1"><em>系统将自动过滤回答中的手机,邮箱,QQ等联系信息</em> <input name="" type="submit"
                                                                                       class="button_answer"
                                                                                       value="提交回答"></div>
                        <?php echo form_close() ?></div>
                </div>
                <?php } else if ($this->wen_auth->get_role_id() == 3) { ?>
                <div class="question_answere_box">
                    <h4><a class="colosequestion" title="点击关闭问题">只有用医师账户登录才能回答问题</a></h4>
                </div>
                <?php }?>

            <?php if ($questions[0]['fUid'] == $uid) { ?>
            <div class="question_answere_box">
                <h4 id="acfrom"><a class="colosequestion" data-id="<?php echo $questions[0]['id'] ?>" title="点击关闭问题">关闭问题</a>
                </h4>
            </div>
            <?php } ?>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>
</div>
<script type="text/javascript">
    $(function () {
        wen.dealtalk($(".replay"));
        $(".question_nav ul li").click(function () {
            $(".question_nav ul li").removeClass();
            $(this).addClass("on");
        });
        $("#acfrom").click(function () {
            $("#formcontroll").toggle(300);
        });
        /*$(".button_answer").click(function (){
            var str = $("#myaser").val();
            if(str.indexOf('联系方式') == -1){
                
                return true;
            }else{
                alert('出现非法字符');
                return false;
            }
            
        });*/
    });
</script>