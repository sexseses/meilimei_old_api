<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| wen Auth Config
| -------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Website details
|--------------------------------------------------------------------------
|
| These details are used in email sent by wen Auth library.
|
*/

$config['wen_website_name'] = 'Your Website';
$config['wen_webmaster_email'] = 'webmaster@yourhost.com';

/*
|--------------------------------------------------------------------------
| Database table
|--------------------------------------------------------------------------
|
| Determines table that used by wen Auth.
|
| 'wen_table_prefix' allows you to specify table prefix that will be use by the rest of the table.
|
| For example specifying 'wen_' in 'wen_table_prefix' and 'users' in 'wen_users_table',
| will make wen Auth user 'wen_users' as users table.
|
*/

$config['wen_table_prefix'] = '';
$config['wen_users_table'] = 'users';
$config['wen_user_profile_table'] = 'user_profile';
$config['wen_user_temp_table'] = 'user_temp';
$config['wen_user_autologin'] = 'user_autologin';
$config['wen_roles_table'] = 'roles';
$config['wen_permissions_table'] = 'permissions';
$config['wen_login_attempts_table'] = 'login_attempts';

/*
|--------------------------------------------------------------------------
| Password salt
|--------------------------------------------------------------------------
|
| You can add major salt to be hashed with password.
| For example, you can get salt from here: https://www.grc.com/passwords.htm
|
| Note:
|
| Keep in mind that if you change the salt value after user registered,
| user that previously registered cannot login anymore.
|
*/

$config['wen_salt'] = '';

/*
|--------------------------------------------------------------------------
| Registration related settings
|--------------------------------------------------------------------------
|
| 'wen_email_activation' = Requires user to activate their account using email after registration.
| 'wen_email_activation_expire' = Time before users who don't activate their account getting deleted from database. Default is 48 Hours (60*60*24*2).
| 'wen_email_account_details' =  Email account details after registration, only if 'wen_email_activation' is FALSE.
|
*/

$config['wen_email_activation'] = FALSE;
$config['wen_email_activation_expire'] = 60*60*24*2;
$config['wen_email_account_details'] = FALSE;

/*
|--------------------------------------------------------------------------
| Login settings
|--------------------------------------------------------------------------
|
| 'wen_login_using_username' = Determine if user can use username in username field to login.
| 'wen_login_using_email' = Determine if user can use email in username field to login.
|
| You have to set at least one of settings above to TRUE.
|
| 'wen_login_record_ip' = Determine if user IP address should be recorded in database when user login.
| 'wen_login_record_time' = Determine if time should be recorded in database when user login.
|
*/

$config['wen_login_using_username'] = TRUE;
$config['wen_login_using_email'] = TRUE;
$config['wen_login_record_ip'] = TRUE;
$config['wen_login_record_time'] = TRUE;

/*
|--------------------------------------------------------------------------
| Auto login settings
|--------------------------------------------------------------------------
|
| 'wen_autologin_cookie_name' = Determine auto login cookie name.
| 'wen_autologin_cookie_life' = Determine auto login cookie life before expired. Default is 2 months (60*60*24*31*2).
|
*/

$config['wen_autologin_cookie_name'] = 'autologin';
$config['wen_autologin_cookie_life'] = 3600*24*31*2;

/*
|--------------------------------------------------------------------------
| Login attempts
|--------------------------------------------------------------------------
|
| 'wen_count_login_attempts' = Determine if wen Auth should count login attempt when user failed to login.
| 'wen_max_login_attempts' =  Determine max login attempt before function is_login_attempt_exceeded() returning TRUE.
|
*/

$config['wen_count_login_attempts'] = TRUE;
$config['wen_max_login_attempts'] = 1;

/*
|--------------------------------------------------------------------------
| Forgot password settings
|--------------------------------------------------------------------------
|
| 'wen_forgot_password_expire' = Time before forgot password key become invalid. Default is 15 minutes (900 seconds).
|
*/

$config['wen_forgot_password_expire'] = 900;

/*
|--------------------------------------------------------------------------
| Captcha
|--------------------------------------------------------------------------
|
| You can set catpcha that created by wen Auth library in here.
| 'wen_captcha_directory' = Directory where the catpcha will be created.
| 'wen_captcha_fonts_path' = Font in this directory will be used when creating captcha.
| 'wen_captcha_font_size' = Font size when writing text to captcha. Leave blank for random font size.
| 'wen_captcha_grid' = Show grid in created captcha.
| 'wen_captcha_expire' = Life time of created captcha before expired, default is 3 minutes (180 seconds).
| 'wen_captcha_expire' = Determine captcha case sensitive or not.
|
*/

$config['wen_captcha_path'] = './captcha/';
$config['wen_captcha_fonts_path'] = $config['wen_captcha_path'].'fonts';
$config['wen_captcha_width'] = 320;
$config['wen_captcha_height'] = 95;
$config['wen_captcha_font_size'] = '';
$config['wen_captcha_grid'] = TRUE;
$config['wen_captcha_expire'] = 180;
$config['wen_captcha_case_sensitive'] = TRUE;

/*
|--------------------------------------------------------------------------
| reCAPTCHA
|--------------------------------------------------------------------------
|
| If you are planning to use reCAPTCHA function, you have to set reCAPTCHA key here
| You can get the key by registering at http://recaptcha.net
|
*/

$config['wen_recaptcha_public_key'] = '';
$config['wen_recaptcha_private_key'] = '';


/*
|--------------------------------------------------------------------------
| URI
|--------------------------------------------------------------------------
|
| Determines URI that used for redirecting in wen Auth library.
| 'wen_deny_uri' = Forbidden access URI.
| 'wen_login_uri' = Login form URI.
| 'wen_activate_uri' = Activate user URI.
| 'wen_reset_password_uri' = Reset user password URI.
|
| These value can be accessed from wen Auth library variable, by removing 'wen_' string.
| For example you can access 'wen_deny_uri' by using $this->dx_auth->deny_uri in controller.
|
*/

$config['wen_deny_uri'] = '/info/deny/';
$config['wen_login_uri'] = '/administrator/login/';
$config['wen_banned_uri'] = '/auth/banned/';
$config['wen_activate_uri'] = '/auth/activate/';
$config['wen_reset_password_uri'] = 'users/reset_password/';


/*
|--------------------------------------------------------------------------
| Helper configuration
|--------------------------------------------------------------------------
|
| Configuration below is actually not used in function in wen_Auth library.
|	They just used to help you coding more easily in controller.
|	You can set it to blank if you don't need it, or even delete it.
|
| However they can be accessed from wen Auth library variable, by removing 'wen_' string.
| For example you can access 'wen_register_uri' by using $this->dx_auth->register_uri in controller.
|
*/

// Registration
$config['wen_allow_registration'] = TRUE;
$config['wen_captcha_registration'] = TRUE;

// Login
$config['wen_captcha_login'] = FALSE;

// URI Locations
$config['wen_logout_uri'] = '/auth/logout/';
$config['wen_register_uri'] = '/auth/register/';
$config['wen_forgot_password_uri'] = '/auth/forgot_password/';
$config['wen_change_password_uri'] = '/auth/change_password/';
$config['wen_cancel_account_uri'] = '/auth/cancel_account/';

// Forms view
$config['wen_login_view'] = 'auth/login_form';
$config['wen_register_view'] = 'auth/register_form';
$config['wen_forgot_password_view'] = 'auth/forgot_password_form';
$config['wen_change_password_view'] = 'auth/change_password_form';
$config['wen_cancel_account_view'] = 'auth/cancel_account_form';

// Pages view
$config['wen_deny_view'] = 'auth/general_message';
$config['wen_banned_view'] = 'auth/general_message';
$config['wen_logged_in_view'] = 'auth/general_message';
$config['wen_logout_view'] = 'auth/general_message';

$config['wen_register_success_view'] = 'auth/general_message';
$config['wen_activate_success_view'] = 'auth/general_message';
$config['wen_forgot_password_success_view'] = 'auth/general_message';
$config['wen_reset_password_success_view'] = 'auth/general_message';
$config['wen_change_password_success_view'] = 'auth/general_message';

$config['wen_register_disabled_view'] = 'auth/general_message';
$config['wen_activate_failed_view'] = 'auth/general_message';
$config['wen_reset_password_failed_view'] = 'auth/general_message';

?>