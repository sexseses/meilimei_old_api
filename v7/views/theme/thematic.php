<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/new.css"> 
</div> 
        <div class="page_contentnew">
        <h5 style="background:#1793ee;color:#fff;font-size:16px;padding:10px;margin-bottom:10px;">医疗美容专题</h5>
        <div class="zhuantilist">
        <?php $i=1; foreach($results as $r){
			echo '<dl class="'.($i%2==0?'odd':'').'">
        <dt>'.$r->title.'</dt>
        <dd><img src="'.$r->picture.'" width="450" height="215" /><p>'.$r->descm.'
</p><span><em>'.date('Y-m-d',$r->cdate).'</em><a href="'.site_url('thematic/detail/'.$r->id).'">查看详情》</a></span></dd> 
        </dl>';$i++;
		}
		echo '<div style="clear:both; "  class="pagelink">'.$pagelink.'</div>';  
		?> 
        </div>
       <div style="clear:both;"> </div>
        </div>
<script>$(function(){  }); </script>