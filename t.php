<?php

function _encode($password) {
$majorsalt = '';
$_pass = str_split($password);
// encrypts every single letter of the password
foreach ($_pass as $_hashpass) {
$majorsalt .= md5($_hashpass);
}
return md5($majorsalt);
}

echo _encode('111111');
echo "<br/>";
echo crypt(_encode('111111'));
phpinfo();

?>