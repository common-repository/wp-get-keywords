<?php
date_default_timezone_set('Asia/Shanghai');

global $wpdb;
define('WP_GET_KEYWORD_TABLENAME',$wpdb->prefix .'getkeyword');

if($wpdb->get_var("SHOW TABLES LIKE '".WP_GET_KEYWORD_TABLENAME."'") != WP_GET_KEYWORD_TABLENAME) {  
/* 数据库名 */
$create_table_sql = "CREATE TABLE `".WP_GET_KEYWORD_TABLENAME."` (
`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`keyword` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`url` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`se` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`ip` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`beforeurl` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = MYISAM   DEFAULT CHARSET=utf8";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($create_table_sql);
}
//搜索引擎名称
global $sename;

//是否有搜索
global $is_search_in;
$is_search_in = false;

//是否有搜索
global $search_keyword;
$search_keyword = '';

?>