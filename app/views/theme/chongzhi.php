<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">充值</a></li>
                            </ul>
                        </div>
                        <div class="rechargeable" ><?php echo form_open('chongzhi/process',array('target' => '_blank' )); ?>
                        	<div class="balance">账户余额：<span><?php echo $amount ?></span>元</div>
                        	<div class="note_box">你的账户余额支付宝可实时到账，支付前请确认您已经开通支付宝，并确保金额不超过您的支付限额</div>
                            <div class="amount">充值金额：<input id="total_fee" name="total_fee" type="text" maxlength="6">元</div>
                            <div class="note_box">
                           	  <h5>限时优惠<span>备注：充值其他金额不赠送（如：充值400不赠送刷新券）</span></h5> 
                                  <p>
                                    <label> 
                                      <input type="radio" class="sepecialradio" name="amounts" value="100" id="RadioGroup1_0">
                                      充值100元送10元</label>
                                    <br>
                                    <label>
                                      <input type="radio" class="sepecialradio" name="amounts" value="500" id="RadioGroup1_1">
                                      充值500元送100元</label>
                                    <br>
                                    <label>
                                      <input type="radio" class="sepecialradio" name="amounts" value="1000" id="RadioGroup1_2">
                                      充值1000元送300元</label>
                                    <br>
                                </p> 
                            </div>
                            <div class="add_money"><input type="radio" name="pay_bank" value="directPay" checked><img src="http://static.meilimei.com.cn/public/images/logo_Alipay.png" border="0"   /></div>
                          <div class="bank_pay">
                           	<h4>或选择其他方式充值<span>(请确保您的银行卡已开通网银)</span></h4>
                            <ul>
                                <li><input type="radio" name="pay_bank" value="ICBCB2C"><span class="icon ICBC"></span></li>
                                <li><input type="radio" name="pay_bank" value="CMB"><span class="icon CMB"></span></li>
                                <li><input type="radio" name="pay_bank" value="CCB"><span class="icon CCB"></span></li>
                                <li><input type="radio" name="pay_bank" value="BOCB2C"><span class="icon BOC"></span></li>
                                <li><input type="radio" name="pay_bank" value="ABC"><span class="icon ABC"></span></li>
                                <li><input type="radio" name="pay_bank" value="COMM"><span class="icon COMM"></span></li>
                                <li><input type="radio" name="pay_bank" value="PSBC-DEBIT"><span class="icon PSBC"></span></li>
                                <li><input type="radio" name="pay_bank" value="CEBBANK"><span class="icon CEB"></span></li>
                                <li><input type="radio" name="pay_bank" value="SPDB"><span class="icon SPDB"></span></li>
                                <li><input type="radio" name="pay_bank" value="GDB"><span class="icon GDB"></span></li>
                                <li><input type="radio" name="pay_bank" value="CITIC"><span class="icon CITIC"></span></li>
                                <li><input type="radio" name="pay_bank" value="CIB"><span class="icon CIB"></span></li>
                                <li><input type="radio" name="pay_bank" value="SDB"><span class="icon SDB"></span></li>
                                <li><input type="radio" name="pay_bank" value="CMBC"><span class="icon CMBC"></span></li>
                                <li><input type="radio" name="pay_bank" value="BJBANK"><span class="icon BJBANK"></span></li>
                                <li><input type="radio" name="pay_bank" value="HZCBB2C"><span class="icon HZCB"></span></li>
                                <li><input type="radio" name="pay_bank" value="SHBANK"><span class="icon SHBANK"></span></li>
                                <li><input type="radio" name="pay_bank" value="BJRCB"><span class="icon BJRCB"></span></li>
                                <li><input type="radio" name="pay_bank" value="SPABANK"><span class="icon SPABANK"></span></li>
                                <li><input type="radio" name="pay_bank" value="FDB"><span class="icon FDB"></span></li>
                                <li><input type="radio" name="pay_bank" value="WZCBB2C-DEBIT"><span class="icon WZCB"></span></li>
                                <li><input type="radio" name="pay_bank" value="NBBANK "><span class="icon NBBANK"></span></li>
                            </ul>
                            <input name="" type="submit" value="进入充值" class="pay_next">
                            
                          </div>
                          <div class="clear" style="clear:both;"></div>  </form>
                      </div> 
                    </div> 
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> <script type="text/javascript">
		$(function(){
			$(".sepecialradio").click(function(){
				$("#total_fee").val($(this).val()) ;
			});
			$(".pay_next").click(function(){
			   tb_show('','dealing?height=100&width=250&inlineId=hiddenModalContent&modal=true');
			});
		}) 
		 </script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script>