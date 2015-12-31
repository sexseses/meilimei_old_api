<!doctype html> 
<html> 
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>权志龙</title>
<link href="http://www.meilimei.com/votes/boilerplate.css" rel="stylesheet" type="text/css">
<link href="http://www.meilimei.com/votes/style.css" rel="stylesheet" type="text/css">
 
<link rel="stylesheet" href="http://www.meilimei.com/votes/jquery.mobile-1.4.0.min.css">
	<script src="http://www.meilimei.com/public/js/jquery.js"></script> 
<script type="text/javascript" src="http://www.meilimei.com/votes/jquery.qtip-1.0.0.min.js"></script>
	<script src="http://www.meilimei.com/votes/jquery.mobile-1.4.0.min.js"></script>
<script src="http://www.meilimei.com/votes/respond.min.js"></script>
<script src="http://www.meilimei.com/votes/jquery.lazyscrollloading-min.js"></script>
<script type="text/javascript">
   var count = 0;
 	$(document).ready(function() {
				 
			 $(".lazyItem").each(function() {
                this.innerHTML = '<img src="'+this.getAttribute("data-ajax")+'">';
            });
             $("button").click(function(){if(count<3){$(this).attr('disabled',"true"); count++;var vid = $(this).attr('data-id');$("#lazyScrollLoading").css("opacity",0.5);$("#popupBasic").show();
				 $.get('<?php echo site_url() ?>vote/ac/<?php echo $param ?>', {"dataid":$(this).attr('data-id')}, function(data){
					 $("#v_"+vid).text(parseInt($("#v_"+vid).text())+1);$("#popupBasic").hide();$("#lazyScrollLoading").css("opacity",1);
					 $("#n_"+vid).text('已成功投票');
				 })}else{$("button").attr('disabled',"true");$("button span").text('已投三次,不能在投票');
				  }
			 })
				}); 
			</script>
</head>
<body>

<div class="gridContainer clearfix"  style="position:relative">    
        <div id="popupBasic" style="left:35%;display:none; width:55px; text-align:center; position:absolute;height:60px;margin:60px auto;" class="ui-body-inherit ui-overlay-shadow ui-corner-all">
			<p><img src="http://www.meilimei.com/images/loading.gif" width="32"></p>
			</div>    
  <div id="div1" class="fluid">
  	<div class="top"><img src="http://www.meilimei.com/votes/images/banner3.png"  width="100%" ></div>
  </div>
  <article class="fluid title">
    <p><strong>街拍 最 VIP</strong><br>
      <strong>VIP, 你们是我沉溺在温暖时光 最耀眼的风景 <br>
      2013.12.23明星闪耀圣诞夜</strong></p>
    <p><strong>街拍VIP</strong><br>
      Fans是随Oppa的这是真理啊,
      <br>
      潮男BigBang的粉丝必须也是引领时尚的啊      </p>
    <p>选出你心里最潮的VIP
      
      <br>
      被选为第一名的VIP有BB的专辑送送送哦!<br>
一个用户只可以投出最最最宝贵的一票, 参与投票的亲亲也有机会获得神秘大大大礼哦!</p>
  </article>
  <div id="lazyScrollLoading"  class="fluid">    
  <li class="fluid content"><img src="http://www.meilimei.com/votes/images/001.jpg"></li> 
    <li class="fluid content  " style="margin-top:5px; margin-bottom:20px;"><button  data-id="1" data-textonly="false" data-textvisible="true" data-msgtext="" data-inline="true" class="show-page-loading-msg ui-btn ui-corner-all" data-rel="popup"  data-transition="pop"><span id="n_1">点个赞，投一票</span></button> 已有票数:<span id="v_1"><?php echo $res[1]['votes'] ?></span></span> 排名:<?php echo $res[1]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/002.jpg"> </li> 
    <li class="fluid content  " style="margin-top:5px; margin-bottom:20px;"><button data-id="2" class="ui-btn ui-corner-all"><span id="n_2">点个赞，投一票</span></button> 已有票数:<span id="v_2"><?php echo $res[2]['votes'] ?></span> 排名:<?php echo $res[2]['order'] ?></li>
    
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/004.jpg"></li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="3" class="ui-btn ui-corner-all"><span id="n_3">点个赞，投一票</span></button> 已有票数:<span id="v_3"><?php echo $res[3]['votes'] ?></span> 排名:<?php echo $res[3]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/005.jpg"> </li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="4" class="ui-btn ui-corner-all"><span id="n_4">点个赞，投一票</span></button> 已有票数:<span id="v_4"><?php echo $res[4]['votes'] ?></span> 排名:<?php echo $res[4]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/007.jpg"> </li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="5" class="ui-btn ui-corner-all"><span id="n_5">点个赞，投一票</span></button> 已有票数:<span id="v_5"><?php echo $res[5]['votes'] ?></span> 排名:<?php echo $res[5]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/008.jpg"></li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="6" class="ui-btn ui-corner-all"><span id="n_6">点个赞，投一票</span></button> 已有票数:<span id="v_6"><?php echo $res[6]['votes'] ?></span> 排名:<?php echo $res[6]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/009.jpg"> </li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="7" class="ui-btn ui-corner-all"><span id="n_7">点个赞，投一票</span></button> 已有票数:<span id="v_7"><?php echo $res[7]['votes'] ?></span> 排名:<?php echo $res[7]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/010.jpg"> </li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="8" class="ui-btn ui-corner-all"><span id="n_8">点个赞，投一票</span></button> 已有票数:<span id="v_8"><?php echo $res[8]['votes'] ?></span> 排名:<?php echo $res[8]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/011.jpg"> </li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="9" class="ui-btn ui-corner-all"><span id="n_9">点个赞，投一票</span></button> 已有票数:<span id="v_9"><?php echo $res[9]['votes'] ?></span> 排名:<?php echo $res[9]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/012.jpg"> </li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="10" class="ui-btn ui-corner-all"><span id="n_10">点个赞，投一票</span></button> 已有票数:<span id="v_10"><?php echo $res[10]['votes'] ?></span> 排名:<?php echo $res[10]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/014.jpg"> </li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="11" class="ui-btn ui-corner-all"><span id="n_11">点个赞，投一票</span></button> 已有票数:<span id="v_11"><?php echo $res[11]['votes'] ?></span> 排名:<?php echo $res[11]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/015.jpg"> </li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="12" class="ui-btn ui-corner-all"><span id="n_12">点个赞，投一票</span></button> 已有票数:<span id="v_12"><?php echo $res[12]['votes'] ?></span> 排名:<?php echo $res[12]['order'] ?></li>
    <li class="fluid content lazyItem" data-ajax="http://www.meilimei.com/votes/images/016.jpg"> </li> 
    <li class="fluid content" style="margin-top:10px; margin-bottom:20px;"><button data-id="13" class="ui-btn ui-corner-all"><span id="n_13">点个赞，投一票</span></button> 已有票数:<span id="v_13"><?php echo $res[13]['votes'] ?></span> 排名:<?php echo $res[13]['order'] ?></li>
  </div>   
 </div>
</body>
</html>
