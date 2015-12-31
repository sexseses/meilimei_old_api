<div class="foot">2015 © 版权所有 - 美丽诊所 - <strong>客服电话：400 6677 245 </strong> 沪ICP备12039527号-1</div></body></html><script language="javascript">
 var noticesec = 1;  
 function SetRemainTime(){ 
	  if (noticesec <= 0) {   jQuery('.clsShow_Notification').slideUp(300);
 
       window.clearInterval(InterValObj);   
	 }else{
		 noticesec--;
		 }
}
jQuery(document).ready(function() { 
if(jQuery('.clsShow_Notification')){
	 InterValObj = window.setInterval(SetRemainTime, 1000); 
}  
})</script>