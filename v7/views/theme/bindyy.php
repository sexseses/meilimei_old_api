<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/AutoSuggest_2.1.3_comp.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/autosuggest_inquisitor.css" ><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">绑定机构</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"> <?php echo form_open_multipart("",array('id' => 'reg'))?>
                        	<ul>
                            	<li>
                                	<div class="k1" style="font-weight:normal"> 机构：</div>
                                    <div class="k2"><span> </span> <p> <input type="hidden" id="companyid" name="companyid" value="<?php echo $userid ?>" /><input  id="company" class="inputbox" type="text" name="company" style="padding:2px; width:230px;" value="<?php echo $name ?>" /></p></div>
                                </li>
                            	 
                                 <li>
                                	<div class="k1" style="font-weight:normal">  </div>
                                    <div class="k2"><input type="submit" name="save" value="保存" style="padding:2px 3px;cursor:pointer;"/></div>
                                </li>
                                <div class="clear" style="clear:both;"></div>
                            </ul>  </form>
                        </div>
                    </div> <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> <script type="text/javascript"> 
			var options = {
		script:"<?php echo site_url('jquery/getsuggest') ?>?json=true&limit=6&",
		varname:"input",
		json:true,
		shownoresults:false,
		maxresults:6,
		callback: function (obj) {  document.getElementById('companyid').value = obj.id; }
	};
	var as_json = new bsn.AutoSuggest('company', options);</script>