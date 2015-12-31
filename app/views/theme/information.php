 <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/bjqs.css"> <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/new.css"><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/bjqs-1.3.min.js"></script><div style="padding:0px 0px 10px">  
            </div>
		</div> 
        <div class="page_contentnew">
        <div class="page_left"> 
        <div id="my-slideshow">
    <ul class="bjqs">
    <?php foreach($banner as $r){
		echo '<li><a href="'.$r['link'].'"><img src="'.$r['picture'].'" ></a> <p class="bjqs-caption">'.$r['title'].'</p></li>';
	}?> 
    </ul>
</div> 
		  
  
        <div class="news_lists">
        <?php
		foreach($results as $r){
			echo '<dl><dt><a href="'.site_url('articles/detail/'.$r['id']).'" title="'.$r['title'].'">'.$r['title'].'</a></dt>
        <dd><em>来源：'.$r['laiyuan'].'</em><em>作者：'.$r['alias'].'</em><em>时间：'.date('Y-m-d',$r['cdate']).'</em></dd><dd class="imgd"></dd>
        <dd class="dec">'.$r['title'].'    <a href="'.site_url('articles/detail/'.$r['id']).'">查看全文>></a></dd>
        </dl>';
		}
		echo '<div class="pagelink">'.$pagelink.'</div>';
		?>
        
        </div>
        </div>
        <div class="page_right">
            <div>
                <img src="http://static.meilimei.com.cn/public/images/zzjgxj.png" />
            </div>
            <div>
                <img src="http://static.meilimei.com.cn/public/images/zdbh.png" />
            </div>
            <div>
                <img src="http://static.meilimei.com.cn/public/images/pzsccld.png" />
            </div>
        </div><div  style="clear:both"></div>
        <!--              <div id="articlelist" ></div> 
         <div id="bannerslist"></div> -->
        </div>
        <script>$(function(){$('#my-slideshow').bjqs({
        'height' : 293,
        'width' : 665,'showmarkers': true,
        'responsive' : true,
        'nexttext' : '>>',
        'prevtext' : '<<',
        'showmarkers' : true,
        'centercontrols' : true 
   });

  $.ajax({ type: "GET",url: "<?php echo base_url() ?>articles",async: true,data: "param=<?php echo $this->uri->segment(1) ?>" , success: function(data)
	  {if(data != ''){$("#articlelist").html(data);}}});
	   $.ajax({ type: "GET",url: "<?php echo base_url() ?>banners",async: true,data: "param=<?php echo $this->uri->segment(1) ?>&pos=3" , success: function(data)
	  {if(data != ''){$("#bannerslist").html(data);}}}); 
           }); 
		 </script>
