<?php
$GDATA['WEN_PAGE_TITLE'] = '美丽美 : 美丽神器,美丽助手,美丽诊所. 最好的整形美容和微整形手机APP_医疗美容行业网站平台';
$GDATA['WEN_PAGE_KEYWORDS'] = '美丽美,美丽神器,美丽助手,美丽诊所,整形美容,美丽神器app,美丽助手app,美丽美app,微整形,整形,整容,整形医院,整形医生,整形app,哪里整形好,哪家整形医院好,美容app,整形对比图,美丽诊所app,哪个整形医生好,美容预约app,整形预约,美白针,瘦脸针,隆鼻,双眼皮,开眼角,激光脱毛,无痛脱毛,玻尿酸,黑脸娃娃,上海整形医院,北京整形医院,广州整形医院,深圳整形医院,抽脂,胶原蛋白注射,牙齿纠正,牙齿美白,除皱,隆胸';
$GDATA['WEN_PAGE_DESCRIPTION'] = '美丽美,旗下APP:美丽神器,美丽助手,美丽诊所,美丽美. 最好的整形美容网站和手机APP. 汇集全国知名的微整形医院和整形医生,免费咨询微整形问题并预约整形医师.提供最新的整形服务价格和限时整形特惠信息. 在美丽社区大家还能讨论整形美容的心得,告诉你哪里的整形医院好,哪里的整形医生好.美丽热线:4006677245,欢迎咨询.';
$this->load->view('theme/menu.php',$GDATA);
if ($wen_msg = $this->session->flashdata('msg')) {
    echo $wen_msg;
}?>
<div class="page_content2">
    <div class="main_slide">
        <div class="iphone_show"><img src="http://static.meilimei.com.cn/public/images/phpwtw.png" width="313" height="300">
        </div>
        <div class="advertise">
            <img src="http://static.meilimei.com.cn/public/images/adwords.png">
            <a style="display:block" target="_blank" class="iphonedownload"
               href="http://itunes.apple.com/cn/app/id654644428/"></a>
             <a style="display:block" target="_blank" class="andrdownload"
               href="http://www.meilimei.com/m/meilishenqi.apk"></a>
            <div class="clear" style="clear:both;"></div>

        </div>
    </div>
</div>
</div>
<div id="newindexlist">
    <div class="leftpos"><h3>最新问题<em style="padding-left:500px"><a
        href="<?php echo site_url('user/dashboard');?>">更多问题</a></em></h3>
        <ul>
            <?php foreach ($results as $r) { 
            echo '<li><a class="thumbs"><img width="60px" src="http://pic.meilimei.com.cn/thumb/' . $r->uid . '_60"/></a><div class="rcontent"><dl><dt><a>' . $r->alias . '</a> 解答了问题</dt><dd class="title"><a href="' . site_url('question/' . $r->id) . '">' . $r->title . '</a></dd><dd class="info"><span class="compans">来自 ' . ($r->company ? $r->company : '未知') . '</span><span class="time">' . (time() - $r->cdate < 600 ? date('i', time() - $r->cdate) . '分钟前' : date('Y-m-d', $r->cdate)) . '</span></dd></dl></div></li>';
        }
            ?>
        </ul>

    </div>
    <div class="rightpos">
        <div class="stags qasks clearfix"><h3 class="qask">快速提问<em>可以输入50个字</em></h3>
            <dl>
                <dd>
                    <?php echo form_open_multipart('', array('name' => 'mpost', 'onsubmit' => 'return check()')); ?>
                    <textarea id="qtitle" name="qtitle"></textarea><span class="counttext"><i>0</i>/50</span>

                    <div class="moreitems"><span>补充问题（选填）<em></em></span>
                        <ul id="morequestion" style="display:none">
                            <li>描述:<br><textarea name="qdes"></textarea></li>
                            <li>图片:<br><input type="file" name="attachPic"/></li>
                        </ul>
                    </div>
                    <button class="subquest" type="submit"></button>
                    </form>
                </dd>
            </dl>
            <div style="clear:both;"></div>
        </div>
        <div class="cstags clearfix"><h3>热门分类<a href="/user/dashboard">更多分类</a></h3>
            <dl>
                <dd><a href="/user/dashboard">除皱</a></dd>
                <dd><a href="/user/dashboard">面部轮廓</a></dd>
                <dd><a href="/user/dashboard">减肥塑形</a></dd>
                <dd><a href="/user/dashboard">鼻部</a></dd>
                <dd><a href="/user/dashboard">口唇</a></dd>
                <dd><a href="/user/dashboard">私密整形</a></dd>
                <dd><a href="/user/dashboard">牙齿</a></dd>
            </dl>
        </div>
        <div id="bannerslist"></div>
    </div>
</div>
<script>$("#qtitle").keydown(function () {
    textCounter($(this), $(".counttext i"), 50);
});
function textCounter(c, b, a) {
    if (c.val().length > a) {
        c.val(c.val().substring(0, a))
    } else {
        b.html(a - c.val().length)
    }
}
; $.ajax({ type: "GET",url: "<?php echo base_url() ?>banners",async: true,data: "param=index&pos=3" , success: function(data)
	  {if(data != ''){$("#bannerslist").html(data);}}});
$(".moreitems em").click(function () {
    $("#morequestion").toggle(300);
});
function check() {
    if ($("#qtitle").val().length < 5) {
        alert('问题填写需大于5个字符');
        return false;
    }
}</script>
<div class="notice_info">
    <ul>
        <li>
            <img src="http://static.meilimei.com.cn/public/images/icon1.png" width="95" height="95">

            <h2>害羞到整形机构咨询</h2>
            <h5>手机使用美丽诊所直接<br><span>线上咨询</span><br>你身边的整形医师</h5>
        </li>
        <li>
            <img src="http://static.meilimei.com.cn/public/images/icon2.png" width="95" height="95">

            <h2>想了解更多的美容<br>整形信息</h2>
            <h5><span>美丽</span>诊所涵盖全面的<span>资讯</span><br>让你在指尖私密地阅览<br>整形介绍和价格等</h5>
        </li>
        <li>
            <img src="http://static.meilimei.com.cn/public/images/icon3.png" width="95" height="95">

            <h2>分不清哪家整形医院<br>或医师更好</h2>
            <h5>得益于一套<span>公平中立的评价体系</span><br>我们让你看到整形行业<br>真实的那一面</h5>
        </li>
        <li class="on">
            <img src="http://static.meilimei.com.cn/public/images/icon4.png" width="95" height="95">

            <h2>想找人聊聊变美的<br>心得和效果</h2>
            <h5>美丽诊所专注于爱美之人的圈子<br>让大家私密而无所忌惮地<br><span>讨论美丽</span></h5>
        </li>
    </ul>
</div>
<div class="listindex"><h3
    style="padding:5px 0px 5px 10px;border-bottom:1px solid #D6D6D6; font-family: '微软雅黑';font-weight:normal">已加入知名机构<b
    style="padding-left:758px;font-weight:normal;font-size:14px;"><a style="color:#ea466e;">更多机构</a></b></h3>

    <div style="background:#fff;width:100%;height:113px;">
        <ul>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/1.gif"><a>上海仁爱医院</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/2.gif"><a>上海时光整形</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/3.gif"><a>艺星整形</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/4.gif"><a>美联臣医疗美容医院</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/5.gif"><a>上海天大医疗
                美容</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/6.gif"><a>上海天美医疗
                美容医院</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/7.gif"><a>泰易瑞亚 </a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/8.gif"><a>美好时光</a></li>
        </ul>
    </div>
</div>

<div class="listindex"><h3
    style="padding:5px 0px 5px 10px;border-bottom:1px solid #D6D6D6; font-family: '微软雅黑';font-weight:normal">合作媒体<b
    style="padding-left:810px;font-weight:normal;font-size:14px;"><a style="color:#ea466e;">更多机构</a></b></h3>

    <div style="background:#fff;width:100%;height:113px;">
        <ul>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/w1.gif"><a>新浪</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/w2.gif"><a>人人网</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/w3.gif"><a>腾讯</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/w4.gif"><a>租了啦</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/w5.gif"><a>新浪微博</a></li>
            <li><img height="48px" src="http://static.meilimei.com.cn/images/w6.gif"><a>腾讯微博</a></li>
            <li><img height="48px" width="90px" src="http://www.xishiqu.com/images/index/logo.jpg"><a href="http://www.xishiqu.com/">西十区</a></li>
            <li><img height="48px" width="90px" src="http://cyzs.yourdream.cc/media/images/web/logo-new.png?0301"><a href="http://ichuanyi.com/">穿衣助手</a></li>
        </ul>
    </div>
</div>
<?php $this->load->view('theme/footer.php'); ?>
