<?php
require_once((dirname(__FILE__)) . '/wp-get-keywords-config.php');
function wp_get_keywords_get_is_search_in()
{
	global $is_search_in;
	return $is_search_in;
}

function wp_get_keywords_clear_search()
{
	global $is_search_in;
	global $search_keyword;
	$is_search_in = false;
	$search_keyword = '';
}

function wp_get_keywords_set_search_in($v)
{
	global $is_search_in;
	$is_search_in = $v;
}

function wp_get_keywords_set_keyword($v)
{
	global $search_keyword;
	$search_keyword = $v;
}

function wp_get_keywords_get_keyword()
{
	global $search_keyword;
	return $search_keyword;
}
/*
	获取链接
*/
function wp_get_keywords_show()
{
	//搜索引擎名称
	global $sename;
	$refer = $_SERVER["HTTP_REFERER"];
	//$refer = "http://www.google.com.hk/search?q=%E9%BB%84%E8%81%AASEM&ie=utf-8&oe=utf-8&aq=t&rls=org.mozilla:zh-CN:official&client=firefox-a";
	$keyword = wp_get_keywords_getkeyword($refer);
	if($keyword != "")
	{
		global $wpdb;
		global $is_search_in;
		global $search_keyword;
		
		$is_search_in = true;
		$search_keyword = $keyword;
		
		//如果有搜索词，就添加到数据库
		$se = $sename;
		$ip = $_SERVER["REMOTE_ADDR"];
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$values = array(
		'keyword'=> $keyword,
		'url'=>$url,
		'se'=>$se,
		'ip'=>$ip,
		'beforeurl'=>$refer);
		$add_styles = array();
		foreach ($values as $value){array_push($add_styles, '%s');}
		//插入数据库
		$wpdb->insert(WP_GET_KEYWORD_TABLENAME,$values,$add_styles);
	}
}

/*
	通过进入链接获取关键词
*/
function wp_get_keywords_is_gb2312($str)
{
        for($i=0; $i<strlen($str); $i++) {
                $v = ord( $str[$i] );
                if( $v > 127) {
                        if( ($v >= 228) && ($v <= 233) )
                        {
								if(($i+2) >= (strlen($str)- 1)) return true;  // not enough characters
                                $v1 = ord( $str[$i+1] );
                                $v2 = ord( $str[$i+2] );
                                if( ($v1 >= 128) && ($v1 <=191) && ($v2 >=128) && ($v2 <= 191) ) // utf编码
                                        return false;
                                else
                                        return true;
                        }
                }
        }
        return true;
}

function wp_get_keywords_getkeyword($url)
{
	global $sename;
	$refer = $url;
	$refer_string = parse_url($refer, PHP_URL_QUERY);
	parse_str($refer_string, $vars);
	if (strpos($refer, "google")) {
		$sename = "google";
	    $search_terms = $vars['q'];
		$bIsUTF8 = ($vars["ie"] == "GB") ? false : true; 
	}
	else if(strpos($refer, "baidu"))
	{
		$sename = "baidu";
	    $search_terms = $vars['wd']?$vars['wd']:$vars['word'];
		$bIsUTF8 = ($vars["ie"] == "GB") ? false : true; 
	 
	}
	else if(strpos($refer, "yahoo"))
	{
		$sename = "yahoo";
		$search_terms = $vars['p']?$vars['p']:$vars['keyword'];
		$bIsUTF8 = ($vars["ie"] == "GB") ? false : true; 
	}
	else if(strpos($refer, "sogou"))
	{
		$sename = "sogou";
		$search_terms = $vars["query"];
		$bIsUTF8 = false;
	}
	else if(strpos($refer, "bing"))
	{
		$sename = "bing";
	    $search_terms = $vars['q'];
		$bIsUTF8 = true;
	 
	}
	else if(strpos($refer, "youdao"))
	{
		$sename = "youdao";
		$search_terms = $vars['q']?$vars['q']:$vars['keyword'];
		$bIsUTF8 = (strtolower($vars["ue"]) == "utf8") ? true : false;
	}
	else if(strpos($refer, "soso"))
	{
		$sename = "soso";
		$search_terms = $vars["w"];
		$bIsUTF8 = ($vars["ie"] == "GB") ? false : true; 
	}
	else if(strpos($refer, "so.com"))
	{
		$sename = "360";
		$search_terms = $vars["q"];
		$bIsUTF8 = ($vars["ie"] == "GB") ? false : true; 
	}
	 
	//if(!$bIsUTF8)
	//$search_terms =iconv("gb2312","utf-8",$search_terms);
	if(wp_get_keywords_is_gb2312($search_terms)) $search_terms =iconv("gb2312","utf-8",$search_terms);
	return $search_terms;
}

function wp_get_keywords_getcurrent()
{
	global $wpdb;
	$sql = "select * from `".WP_GET_KEYWORD_TABLENAME ."` where `ip`='".$_SERVER["REMOTE_ADDR"]."' and DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(`date`) group by `keyword` order by `date` desc";
	$results = $wpdb->get_results($sql,ARRAY_A);
	foreach ($results as $row) {
		$sekeyword .= $row['keyword'] . ',';
	}
	return rtrim($sekeyword,',');
}
?>