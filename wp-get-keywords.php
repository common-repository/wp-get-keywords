<?php
/* 
Plugin Name: wp-get-keywords
Version: 2.0
Plugin URI: http://hcsem.com/wp-get-keywords/
Description: 获取网站入口关键词，调用代码if(function_exists('keywords_show')){keywords_show();}
Author: 黄聪
Author URI: http://hcsem.com/huangcong/
*/

// 导入必要函数
require_once((dirname(__FILE__)) . '/wp-get-keywords-config.php');
require_once((dirname(__FILE__)) . '/wp-get-keywords-export.php');
require_once((dirname(__FILE__)) . '/wp-get-keywords-functions.php');

add_action('admin_menu','wp_get_keywords_addmenu');
add_action('wp_footer','wp_get_keywords_show');

//调用显示
function wp_get_keywords_get_show($u = '')
{
	$url = $u != '' ? $u : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$sql = "select * from `".WP_GET_KEYWORD_TABLENAME."` where `url`='".$url."' group by `keyword`,`beforeurl` order by date desc";
	
	global $wpdb;
	$results = $wpdb->get_results($sql,ARRAY_A);
	if($results)
	{
		?>
		<style>
			.keywordshand a{cursor: pointer;color:#F00;}
			#keywords li:hover{background-color:#eee;}
			#keywords li a{float:left;width:300px;}
		</style>
		<?
		echo '<div class="keywordshand"><a onclick="dokeywordsPop()">点击查看搜索词</a></div>';
		echo '<div id="keywords" style="display:none;"><ul>';
		foreach ($results as $row) {
			echo '<li><a href="'.$row['beforeurl'] .'" title="'.$row['keyword'].'" target="_blank">'.$row['keyword'].'</a><font>'.$row['date'] .'</font></li>';
		}
		echo '</ul></div>';
		?>
		<script type="text/javascript">
			function dokeywordsPop(){
				if ($("#keywords").is( ":hidden "))
				{
					$("#keywords").show();
				}else
				{
					$("#keywords").hide();
				}
			}
		</script>
		<?
	}
	else
	{echo '没有搜索词。';}
}

//添加后台菜单
function wp_get_keywords_addmenu() {
	add_options_page('WP_GET_Keywords', '获取关键词', 8, __FILE__,'wp_get_keywords_setoption');
}

//设置页面
function wp_get_keywords_setoption(){
	?>
		<div class=wrap>
        <h2>文章编辑页公告设置</h2>
		  <form method="post">
			<input id="buttonFlag" name="buttonFlag" type="hidden" />
			<script>function setFun(val){document.getElementById("buttonFlag").value = val;} </script>
			<p>查询或导出的链接：<input style="width:300px;" type="text" name="WP_GET_URL" value="" /></p>
			<p>
			<input type="button" name="WP_GET_Update" value="查询" onclick="setFun('WP_GET_Update');this.form.submit()"/>
			<input type="button" name="WP_GET_OUT" value="导出" onclick="setFun('WP_GET_OUT');this.form.submit()"/></p>
			<table class="widefat fixed" style="clear:none;">
				<thead>
					<tr>
						<th width="20">序号</th>
						<th width="30">关键词</th>
						<th width="80">URL</th>
						<th width="500">来源</th>
						<th width="40">IP</th>
						<th width="30">时间</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if($_POST["buttonFlag"]=='WP_GET_Update'){
						$url = $_POST['WP_GET_URL'];
						if($url==""){
							$sql = "select * from `".WP_GET_KEYWORD_TABLENAME."` order by date desc";}else
							{
						$sql = "select * from `".WP_GET_KEYWORD_TABLENAME."` where `url` like '%".$url."' order by date desc";
							}
						$index=1;
						//echo $sql;
						global $wpdb;
						$results = $wpdb->get_results($sql,ARRAY_A);
						foreach ($results as $row) {
					?>
					<tr id="item-tr-<?php echo $row['ID']; ?>">
						<td><?php echo $index++; ?></td>
						<td><?php echo $row['keyword']; ?></td>
						<td><?php echo $row['url']; ?></td>
						<td><a target="_blank" href="<?php echo $row['beforeurl']; ?>"><?php echo $row['beforeurl']; ?></a></td>
						<td><?php echo $row['ip']; ?></td>
						<td><?php echo $row['date']; ?></td>
					</tr>
					<?php }
					}
					?>
				</tbody>
			</table>
		   </form>
			
			<?php
			/** Show others information **/
			wp_get_keywords_foot_text();
			?>
		</div>
<?php
}

function wp_get_keywords_foot_text(){
	?>
	<h3>PS:</h3>
	<p>提醒：该插件必须是Wordpress3.3版本以上才可以使用。</p>
	<?php
}
?>