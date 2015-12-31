<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = '10.10.10.5';
$db['default']['username'] = 'web_user';
$db['default']['password'] = 'CD95XwPVrdPsdprr';
$db['default']['database'] = 'meilimei';

$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = false;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;


//链接最土团购
$db['tehui']['hostname'] = '10.10.10.5';
$db['tehui']['username'] = 'web_user';
$db['tehui']['password'] = 'CD95XwPVrdPsdprr';
$db['tehui']['database'] = 'tehui';
$db['tehui']['dbdriver'] = 'mysql';
$db['tehui']['dbprefix'] = '';
$db['tehui']['pconnect'] = false;
$db['tehui']['db_debug'] = TRUE;
$db['tehui']['cache_on'] = FALSE;
$db['tehui']['cachedir'] = '';
$db['tehui']['char_set'] = 'utf8';
$db['tehui']['dbcollat'] = 'utf8_general_ci';
$db['tehui']['swap_pre'] = '';
$db['tehui']['autoinit'] = TRUE;
$db['tehui']['stricton'] = FALSE;


//链接活动表
$db['event']['hostname'] = 'kingsley.mysql.rds.aliyuncs.com';
$db['event']['username'] = 'e_user';
$db['event']['password'] = '123123';
$db['event']['database'] = 'mlm_event';
$db['event']['dbdriver'] = 'mysql';
$db['event']['dbprefix'] = '';
$db['event']['pconnect'] = false;
$db['event']['db_debug'] = TRUE;
$db['event']['cache_on'] = FALSE;
$db['event']['cachedir'] = '';
$db['event']['char_set'] = 'utf8';
$db['event']['dbcollat'] = 'utf8_general_ci';
$db['event']['swap_pre'] = '';
$db['event']['autoinit'] = TRUE;
$db['event']['stricton'] = FALSE;

//链接活动表
$db['event1']['hostname'] = 'meilimeitest.mysql.rds.aliyuncs.com';
$db['event1']['username'] = 'test_user';
$db['event1']['password'] = '123123';
$db['event1']['database'] = 'mlm_event';
$db['event1']['dbdriver'] = 'mysql';
$db['event1']['dbprefix'] = '';
$db['event1']['pconnect'] = false;
$db['event1']['db_debug'] = TRUE;
$db['event1']['cache_on'] = FALSE;
$db['event1']['cachedir'] = '';
$db['event1']['char_set'] = 'utf8';
$db['event1']['dbcollat'] = 'utf8_general_ci';
$db['event1']['swap_pre'] = '';
$db['event1']['autoinit'] = TRUE;
$db['event1']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */
