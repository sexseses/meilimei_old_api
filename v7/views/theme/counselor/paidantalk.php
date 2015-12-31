<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script>

<style type="text/css">#track li,dd{display:block;width:100%;margin:10px auto;line-height:30px;min-height:30px;}#track li{margin:auto;text-align:left;} dd label{display:inline-block;width:100px;}</style>
                    	 
  <div class="manage_yuyue" style="padding:20px;width:90%" >
   	  <div>
        <form method="post"><dl><input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash() ?>" />
                <dd> <textarea name="talks" cols="60" rows="2"></textarea></dd>
                <dd><button type="submit" name="submit" style="width:90px;height:26px;">添加备注</button>
                </dl> 
        </form>
          <div class="clear" style="clear:both;"></div>
        <ul id="track">
          <?php   
							 foreach($talk as $r){  
								   echo '<li> 【'.($user[0]->id==$r->fuid?$user[0]->alias:'客服').'】 '.$r->message .' - 日期:'.date('Y-m-d H:i:s',$r->cdate).'</li>';
								   
							 }
							 ?>
        </ul><div class="clear" style="clear:both;"></div>
      </div>
                             
  </div>
 