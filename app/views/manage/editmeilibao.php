<style type="text/css">
table{border-collapse:collapse;border-spacing:0;border-left:1px solid #888;border-top:1px solid #888;background:#efefef;}
th,td{border-right:1px solid #888;border-bottom:1px solid #888;padding:5px 15px;}
th{font-weight:bold;background:#ccc;}
</style>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/AutoSuggest_2.1.3_comp.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/autosuggest_inquisitor.css" ><div class="page_content933"><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script>
            	<div class="institutions_info new_institutions_info">
                	<?php  $this->load->view('manage/leftbar'); ?>
                    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li class="selected">用户基本资料</li> 
                            </ul>
                        </div>
                        <div class="personal_information"><?php
						 $attributes = array('id' => 'usersubmit'); 
						 echo form_open_multipart("manage/meilibao/update",$attributes); ?>
                        	<ul>
                            	<li><input type="hidden" name="user_id" value="<?php echo isset($user_id)? $user_id:''; ?>" />
                                	<div class="k1"><span>*</span>姓名(真实)：</div>
                                    <div class="k2"><p><input class="inputbox" type="text" id="name" name="name" value="<?php echo isset($name)? $name:''; ?>" /></p></div>
                                </li>
                               
                                <li>
                                	<div class="k1"><span>*</span>证件号码：</div>
                                    <div class="k2"><p><input class="inputbox" type="text" id="id_card" name="id_card" value="<?php echo isset($id_card)? $id_card:''; ?>" /></p></div>
                                </li>

                                <li>
                                	<div class="k1"> <span>*</span>性别：</div>
                                    <div class="k2"><p> <input type="radio" <?php echo (!isset($sex)||$sex=='1')?'checked="checked"':'' ?> value="1" name="sex" />女 <input name="sex" type="radio" <?php echo (isset($sex)&&$sex=='2')?'checked="checked"':'' ?> value="2" />男</p></div>
                                </li>
                                <li>
                                	<div class="k1"> <span>*</span>出生日期：</div>
                                    <div class="k2"><p><input class="datepicker" type="text" name="birthday" value="<?php echo isset($birthday)?$birthday:'' ?>" /> </p></div>
                                </li>
                               <li>
                                	<div class="k1"><span>*</span> 常驻城市：</div>
                                     <div class="k2"><p>
										<select id="selProvince" name="selProvince">
											<option value="0">省/直辖市</option>
										</select>
										<select id="selCity" name="selCity">
											<option value="0">城市</option>
										</select>
									 <input class="inputbox" type="hidden" name="city" id="allcity" value="<?php echo isset($city)? $city:''; ?>" /> </p></div>
                                </li>
                                <li>
                                	<div class="k1"> <span>*</span>手机号码：</div>
                                     <div class="k2"><p><input maxlength="11" class="inputbox" type="text" id="telphone" name="telphone" value="<?php echo isset($telphone)? $telphone:''; ?>" /> </p></div>
                                </li>
                                <li>
                                	<div class="k1"> <span>*</span>投保状态：</div>
                                    <div class="k2"><p> <input type="radio" <?php echo (!isset($tbzt_status)||($tbzt_status=='1'))?'checked="checked"':'' ?> value="1" name="tbzt_status" />未投保 <input name="tbzt_status" type="radio" <?php echo (isset($tbzt_status)&&($tbzt_status=='2'))?'checked="checked"':'' ?> value="2" />已投保</p></div>
                                </li>
                                <li>
                                	<div class="k1"> 验证码：</div>
                                    <div class="k2"><p><?php echo isset($capture)?$capture:'';?></p></div>
                                </li>
                                <li>
                                	<div class="k1">客服备注：</div>
                                    <div class="k2"><textarea style="width:500px;height:100px;" name="comment"><?php echo isset($comment)?$comment:''?></textarea></div>
                                </li>                              
                                <li>
                                <div class="k1"> </div>
                                <div class="k2"> <input style="font-size:14px; width:90px; padding:3px 5px;"  type="button" onclick="javascript:window.history.back(-1);" value="关闭" />
                                <input id="normalsubmit" onclick="return false" style="font-size:14px; width:90px; padding:3px 5px;" type="submit" value="保存" />  </div>
                                </li>
                            </ul> <?php echo form_close(); ?>
                        </div>
                    </div>
                    <?php if(!empty($polocy_no)){?>
                    <table broder="1">
                      <thead>
                          <tr>
                              <td>保单号</td>
                              <td>投保时间</td>
                          </tr>
                      </thead>
                     <?php foreach($polocy_no as $v){ ?>
                      <tbody>
                              <tr>
                              <td><?php echo $v['policy_no']?></td>
                              <td><?php echo date('Y-m-d H:i:s',$v['creat_time'])?></td>
                              </tr>
                      </tbody>
                    <?php } }?>
                    </table>
                    <div class="clear" style="clear:both;"></div>
                   </div> 
<script type="text/javascript" src="<?php echo base_url("/images/js/jquery.js"); ?>"></script>
<script src="<?php echo base_url("/images/js/city.js"); ?>" type="text/javascript"></script>
<script src="<?php echo base_url("/images/js/province.js"); ?>" type="text/javascript"></script>
<script type="text/javascript"> 
$(document).ready(function(){
	$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
	$('#specsubmit').bind('click',function(){  
	  $("#submittype").val(2);
	  $('#usersubmit').submit();
	});
	
	$('#normalsubmit').bind('click',function(){
		var mname= $('#name').val();
		var patrn= /[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/gi;    
		if (!patrn.exec(mname) || mname.length<2 || mname == ''){
			alert('填写准确姓名');
			return false;    
		}
		
		var cardset = ValidateIdCard($('#id_card').val());
		if(cardset == '0'){
			alert('身份证非法');
			$("#id_card").focus(); 
			return false;
		}
		
		if($('.hasDatepicker').val()==''){
			alert('出身日期不能为空');
			$(".hasDatepicker").focus(); 
			return false;
		}
		
		if($('#selProvince').val()=='0'){
			alert('请选择省份');
			$("#selProvince").focus(); 
			return false;
		}
		
		if($('#selCity').val()=='0'){
			alert('请选择城市');
			$("#selCity").focus(); 
			return false;
		}

		if($('#telphone').val()!='' && $('#telphone').val().length==11){
			if (!$('#telphone').val().match(/1[3458]{1}\d{9}$/)) { 
				alert("请输入有效的手机号码！"); 
				$("#telphone").focus(); 
				return false; 
			}
		}else{
			alert('请输入11位手机号码');
			return false;
		}
	  $('#allcity').val($('#selProvince option:selected').text()+'/'+$('#selCity option:selected').text()); 
	  $("#submittype").val(1);
	  $('#usersubmit').submit();
	}); 
	
	
	$.each(province, function (k, p) {
		var option = "<option value='" + p.ProID + "'>" + p.ProName + "</option>";
		$("#selProvince").append(option);
	});
	 
	 function allselProvince(sheng){
		if(sheng){
				var selValue = sheng;
				$("#selCity option:gt(0)").remove();
				$.each(city, function (k, p) { 
					if (p.ProID == selValue) {
						var option = "<option value='" + p.CityID + "'>" + p.CityName + "</option>";
						$("#selCity").append(option);
					}
				});
		}else{
			$("#selProvince").change(function () {
					var selValue = $(this).val(); 
				$("#selCity option:gt(0)").remove();
				 
				$.each(city, function (k, p) { 
					if (p.ProID == selValue) {
						var option = "<option value='" + p.CityID + "'>" + p.CityName + "</option>";
						$("#selCity").append(option);
					}
				});
			});
		}
	 }
	 allselProvince();
	$("#selCity").change(function () {
		var selValue = $(this).val();
		$("#selDistrict option:gt(0)").remove(); 

		$.each(District, function (k, p) {
			if (p.CityID == selValue) {
				var option = "<option value='" + p.Id + "'>" + p.DisName + "</option>";
				$("#selDistrict").append(option);
			}
		}); 
	});
	
	function ValidateIdCard(sId){
		 var aCity = { 11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江 ", 31: "上海", 32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南", 42: "湖北 ", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州", 53: "云南", 54: "西藏 ", 61: "陕西", 62: "甘肃", 63: "青海", 64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外 " } 
		var iSum = 0; 
		var info = ""; 
		sId = sId.replace(/x$/i, "a"); 
		if (aCity[parseInt(sId.substr(0, 2))] == null) return "0";
		sBirthday = sId.substr(6, 4) + "-" + Number(sId.substr(10, 2)) + "-" + Number(sId.substr(12, 2)); 
		var d = new Date(sBirthday.replace(/-/g, "/")) 
		if (sBirthday != (d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate())) return "0"; 
		for (var i = 17; i >= 0; i--) iSum += (Math.pow(2, i) % 11) * parseInt(sId.charAt(17 - i), 11) 
		if (iSum % 11 != 1) return "0"; 
		var mycard=new Array()
		mycard['y']= sId.substr(6, 4);
		mycard['m']= Number(sId.substr(10, 2));
		mycard['d']= Number(sId.substr(12, 2));
		return mycard;
	}
	
 	var allcity = '<?php echo isset($city)? $city:''; ?>'.split("/");
	var selProvince = $("#selProvince option").length;
	for(var i=0;i<selProvince;i++)  
	{
		if($("#selProvince").get(0).options[i].text == allcity[0]){  
			$("#selProvince").get(0).options[i].selected = true;  
			break; 
		}
	}
	allselProvince($('#selProvince option:selected').val());
	var selCity = $("#selCity option").length;
	for(var i=0;i<selProvince;i++)  
	{
		if($("#selCity").get(0).options[i].text == allcity[1]){  
			$("#selCity").get(0).options[i].selected = true;  
			break; 
		}
	}


})
 </script>

<script type="text/javascript">
 $(function() {
     $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  });   
</script>