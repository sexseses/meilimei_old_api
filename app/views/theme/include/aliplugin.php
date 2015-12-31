<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

    <script type="text/javascript" src="http://g.tbcdn.cn/sj/qn/jssdk.js"></script>

    <script type="text/javascript">
        function regEvent() {
            QN.event.regEvent( {
                eventId : 'wangwang.active_contact_changed',
                success : function(eventId) {
                    //alert("event success");
                },
                error : function(msg, eventId) {
                    //alert("event failed");

                },
                notify : function(data, eventId) {
                    //alert(JSON.stringify(data));
                }
            });

            <?php if($login == 0){?>
                getActiveUser();
            <?php }?>
        }

        function getActiveUser() {
            //alert("getActiveUser ing...");

            QN.application.invoke( {
                cmd : 'getActiveUser',
                param : {},
                error : function(msg, cmd, param) {
                },
                success : function(rsp, cmd, param) {
                    //alert(JSON.stringify(rsp.uid));
                    var uid = getSubString(rsp.uid);

                    if(!isNaN(uid)){
                        window.location.href='/aliplugin/index?uid=' + uid;
                    }else{

                        document.getElementById('alikey').value = uid;
                        window.location.href='/aliplugin/index?uid=' + uid;
                    }
                }
            });
        }

        function getSubString(s) {
            var ss;
            ss = s.substring(8, 100);//截取掉前面8位域信息，剩下的就是OpenIM的自建帐号的userid
            return ss;
        }

    </script>
</head>
<body onload="regEvent();">
<!--在body的onload里执行regEvent事件，确保会执行-->

<?php if($login == 0 ){?>
    <form id="reg" accept-charset="utf-8" method="post" action="<?php echo site_url('aliplugin/index').'?aliplugin=1'; ?>">
        <table style="<?php if($login==1){?>display: none;<?php } ?>">
            <tr>
                <td>手机号：</td>
                <td><input name="uname" onfocus="if(this.value=='请输入你的邮箱或手机号') this.value=''"
                           onblur="if(this.value=='') this.value='请输入你的邮箱或手机号'; " type="text" class="username required"
                           value="请输入你的邮箱或手机号"> <?php echo form_error('uname'); ?></td>
            </tr>
            <tr>
                <td colspan="2" align="center">默认密码:111111</td>
            </tr>
            <tr>
                <td colspan="2" align="center"><?php echo $mobile ;?></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><input name="regbuton" id="regbuton" type="submit" value="注册"></td>
            </tr>
            </table>
            <input name="alikey"  type="hidden" id="alikey"  value=""/>
            <input name="upass"  type="hidden" id="upass"  value="111111"/>
            <input type="hidden" name="utype" id="utype" value="1">
    </form>
<?php }else{?>
    <table>
        <tr>
            <td colspan="2">用户信息</td>
        </tr>
        <tr>
            <td>用户名：</td>
            <td><?php echo $infos[0]['username']?></td>
        </tr>
        <tr>
            <td>用户ID：</td>
            <td><?php echo $infos[0]['ID']?></td>
        </tr>
        <tr>
            <td>OPENIM：</td>
            <td><?php echo $infos[0]['aliplugin']?></td>
        </tr>
        <tr>
            <td>手机号：</td>
            <td><?php echo $infos[0]['phone']; ?></td>

        </tr>
        <tr>
            <td>性别：</td>
            <td><?php echo $infos[0]['sex'];?></td>
        </tr>
        <tr>
            <td>年龄：</td>
            <td><?php echo $infos[0]['age'];?></td>
        </tr>
        <tr>
            <td>城市：</td>
            <td><?php echo $infos[0]['city'];?></td>
        </tr>
        <tr>
            <td>来源平台：</td>
            <td><?php echo $infos[0]['regsys'];?></td>
        </tr>
    </table>
<?php } ?>
</body>
</html>